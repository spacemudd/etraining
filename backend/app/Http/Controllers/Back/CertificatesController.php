<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\CertificateCsvImportJob;
use App\Jobs\IssueCertificateJob;
use App\Models\Back\Course;
use App\Models\Back\CertificatesImport;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
