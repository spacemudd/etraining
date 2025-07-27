<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\CertificateCsvImportJob;
use App\Jobs\IssueCertificateJob;
use App\Models\Back\Course;
use App\Models\Back\CertificatesImport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\Back\Trainee;
use App\Models\Back\CertificatesImportsRow;

class CertificatesController extends Controller
{
    public function import()
    {
        return Inertia::render('Back/Certificates/Import');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required',
        ]);

        $path = request()->file('excel_file')->store('tmp');
        $filepath = storage_path('app').'/'.$path;

        $import = new CertificatesImport();
        $import->course_id = Course::find('d893bf8e-9dfc-41f6-bbe2-5b0ca642974c')->id ?? Course::first()->id;
        $import->status = CertificatesImport::STATUS_IMPORTING;
        $import->processed_count = 0;
        $import->total_count = 0;
        $import->started_at = null;
        $import->completed_at = null;
        $import->filepath = $filepath;
        $import->imported_by_id = auth()->user()->id;
        $import->save();

        dispatch(new CertificateCsvImportJob($import));

        return response()->json($import);
    }

    public function uploadZip(Request $request)
    {
        $request->validate([
            'zip' => 'required|file|mimes:zip',
            'course_id' => 'required|uuid|exists:courses,id',
        ]);

        $zip = new ZipArchive;
        $zipPath = $request->file('zip')->getRealPath();
        $res = $zip->open($zipPath);
        if ($res !== true) {
            return response()->json(['error' => 'Could not open zip file.'], 400);
        }

        $import = new CertificatesImport();
        $import->course_id = $request->course_id;
        $import->status = CertificatesImport::STATUS_IMPORTING;
        $import->processed_count = 0;
        $import->total_count = 0;
        $import->started_at = now();
        $import->completed_at = null;
        $import->filepath = '';
        $import->imported_by_id = auth()->user()->id;
        $import->save();

        $matched = [];
        $unmatched = [];
        $total = $zip->numFiles;
        for ($i = 0; $i < $total; $i++) {
            $entry = $zip->getNameIndex($i);
            if (preg_match('/^(\d+)_.*\.pdf$/i', $entry, $matches)) {
                $identity = $matches[1];
                $trainee = Trainee::withTrashed()->where('identity_number', $identity)->first();
                $pdfContent = $zip->getFromIndex($i);
                if ($trainee) {
                    $s3Path = "certificates/imports/{$import->id}/{$entry}";
                    Storage::disk('s3')->put($s3Path, $pdfContent);
                    $row = new CertificatesImportsRow([
                        'trainee_id' => $trainee->id,
                        'course_id' => $import->course_id,
                        'pdf_path' => $s3Path,
                    ]);
                    $import->rows()->save($row);
                    $matched[] = [
                        'id' => $trainee->id,
                        'identity_number' => $identity,
                        'name' => $trainee->name,
                        'pdf' => $s3Path,
                    ];
                } else {
                    $unmatched[] = [
                        'identity_number' => $identity,
                        'filename' => $entry,
                    ];
                }
            } else {
                $unmatched[] = [
                    'identity_number' => null,
                    'filename' => $entry,
                ];
            }
        }
        $zip->close();
        $import->processed_count = count($matched);
        $import->total_count = $total;
        $import->completed_at = now();
        $import->save();

        return response()->json([
            'import_id' => $import->id,
            'matched' => $matched,
            'unmatched' => $unmatched,
        ]);
    }

    public function finalizeImport(Request $request)
    {
        $request->validate([
            'import_id' => 'required|integer|exists:certificates_imports,id',
            'mappings' => 'required|array',
            'mappings.*.filename' => 'required|string',
            'mappings.*.trainee_id' => 'required|uuid|exists:trainees,id',
        ]);

        $import = CertificatesImport::findOrFail($request->import_id);
        $updated = 0;
        foreach ($request->mappings as $map) {
            $row = $import->rows()->where('pdf_path', 'like', "%/{$map['filename']}")->first();
            if ($row) {
                $row->trainee_id = $map['trainee_id'];
                $row->save();
                $updated++;
            } else {
                // If not found, create a new row for this mapping
                $row = new CertificatesImportsRow([
                    'trainee_id' => $map['trainee_id'],
                    'course_id' => $import->course_id,
                    'pdf_path' => "certificates/imports/{$import->id}/{$map['filename']}",
                ]);
                $import->rows()->save($row);
                $updated++;
            }
        }
        // After all mappings are updated, queue the email job
        $import->status = CertificatesImport::STATUS_SENDING;
        $import->save();
        dispatch(new \App\Jobs\IssueCertificateJob($import));
        return response()->json([
            'success' => true,
            'updated' => $updated,
            'import_id' => $import->id,
        ]);
    }

    public function job($id)
    {
        return Inertia::render('Back/Certificates/Job', [
            'job' => CertificatesImport::withCount('rows')->find($id),
        ]);
    }

    public function issue($id)
    {
        $import = CertificatesImport::find($id);
        if ($import->status != CertificatesImport::STATUS_IMPORTING) {
            return response()->json(['error' => 'Cannot issue certificates for this import.'], 400);
        }
        $import->status = CertificatesImport::STATUS_SENDING;
        $import->save();
        dispatch(new IssueCertificateJob($import));
        return redirect()->route('back.certificates.import.job', $import);
    }
}
