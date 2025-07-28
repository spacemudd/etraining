<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\UkCertificate;
use App\Models\Back\UkCertificateRow;
use App\Models\Back\Course;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use ZipArchive;

class UkCertificatesController extends Controller
{
    public function index()
    {
        $courses = Course::select('id', 'name_ar', 'instructor_id', 'created_at')
            ->with('instructor:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(100);
        return Inertia::render('Back/UkCertificates/Index', [
            'courses' => $courses,
        ]);
    }

    public function uploadZip(Request $request)
    {
        $request->validate([
            'zip' => 'required|file|mimes:zip',
            'course_id' => 'required|exists:courses,id',
        ]);

        $zipFile = $request->file('zip');
        $courseId = $request->input('course_id');

        // Create UK certificate record
        $ukCertificate = UkCertificate::create([
            'course_id' => $courseId,
            'status' => UkCertificate::STATUS_PROCESSING,
            'started_at' => now(),
        ]);

        // Upload ZIP to S3 first
        $zipS3Path = 'uk-certificates/' . $ukCertificate->id . '/original.zip';
        Storage::disk('s3')->put($zipS3Path, file_get_contents($zipFile->getPathname()));

        $matched = [];
        $unmatched = [];
        $totalFiles = 0;

        // Download ZIP from S3 and process
        $zipContent = Storage::disk('s3')->get($zipS3Path);
        $tempZipPath = storage_path('app/temp/' . $ukCertificate->id . '.zip');
        
        // Ensure temp directory exists
        if (!file_exists(dirname($tempZipPath))) {
            mkdir(dirname($tempZipPath), 0755, true);
        }
        
        file_put_contents($tempZipPath, $zipContent);

        $zip = new ZipArchive();
        if ($zip->open($tempZipPath) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                
                // Skip files that start with a dot (hidden/system files)
                if (strpos(basename($filename), '.') === 0) {
                    continue;
                }
                // Skip directories and non-PDF files
                if (substr($filename, -1) === '/' || !str_ends_with($filename, '.pdf')) {
                    continue;
                }

                $totalFiles++;
                
                // Parse filename: {identity_number}_{name}.pdf
                $basename = basename($filename, '.pdf');
                $parts = explode('_', $basename, 2);
                
                if (count($parts) !== 2) {
                    // Invalid filename format - save to DB
                    UkCertificateRow::create([
                        'uk_certificate_id' => $ukCertificate->id,
                        'trainee_id' => null,
                        'identity_number' => '',
                        'trainee_name' => '',
                        'filename' => $filename,
                        'status' => UkCertificateRow::STATUS_FAILED,
                        'error_message' => 'Invalid filename format. Expected: {identity_number}_{name}.pdf',
                    ]);

                    $unmatched[] = [
                        'filename' => $filename,
                        'identity_number' => '',
                        'trainee_name' => '',
                        'reason' => 'Invalid filename format'
                    ];
                    continue;
                }

                $identityNumber = $parts[0];
                $traineeName = $parts[1];

                // Additional validation: identity number and name must not be empty, name must not be just a dot or whitespace
                if (empty($identityNumber) || empty($traineeName) || trim($traineeName) === '.' || trim($traineeName) === '') {
                    UkCertificateRow::create([
                        'uk_certificate_id' => $ukCertificate->id,
                        'trainee_id' => null,
                        'identity_number' => $identityNumber,
                        'trainee_name' => $traineeName,
                        'filename' => $filename,
                        'status' => UkCertificateRow::STATUS_FAILED,
                        'error_message' => 'Invalid identity number or trainee name in filename.',
                    ]);

                    $unmatched[] = [
                        'filename' => $filename,
                        'identity_number' => $identityNumber,
                        'trainee_name' => $traineeName,
                        'reason' => 'Invalid identity number or trainee name in filename',
                    ];
                    continue;
                }

                // Try to find trainee by identity number
                $trainee = Trainee::where('identity_number', $identityNumber)->first();

                if ($trainee) {
                    // Upload PDF to S3
                    $pdfContent = $zip->getFromIndex($i);
                    $s3Path = 'uk-certificates/' . $ukCertificate->id . '/' . $filename;
                    Storage::disk('s3')->put($s3Path, $pdfContent);

                    // Create row record
                    UkCertificateRow::create([
                        'uk_certificate_id' => $ukCertificate->id,
                        'trainee_id' => $trainee->id,
                        'identity_number' => $identityNumber,
                        'trainee_name' => $traineeName,
                        'filename' => $filename,
                        'pdf_path' => $s3Path,
                        'status' => UkCertificateRow::STATUS_PENDING,
                    ]);

                    $matched[] = [
                        'id' => $trainee->id,
                        'name' => $trainee->name,
                        'identity_number' => $identityNumber,
                        'email' => $trainee->email,
                        'filename' => $filename,
                    ];
                } else {
                    // Create unmatched row record
                    UkCertificateRow::create([
                        'uk_certificate_id' => $ukCertificate->id,
                        'trainee_id' => null,
                        'identity_number' => $identityNumber,
                        'trainee_name' => $traineeName,
                        'filename' => $filename,
                        'status' => UkCertificateRow::STATUS_PENDING,
                    ]);

                    $unmatched[] = [
                        'filename' => $filename,
                        'identity_number' => $identityNumber,
                        'trainee_name' => $traineeName,
                        'searchQuery' => '',
                        'searchResults' => [],
                        'selectedTrainee' => null,
                    ];
                }
            }
            $zip->close();
        }

        // Clean up temp file
        if (file_exists($tempZipPath)) {
            unlink($tempZipPath);
        }

        // Update certificate counts
        $ukCertificate->update([
            'total_files' => $totalFiles,
            'matched_count' => count($matched),
            'unmatched_count' => count($unmatched),
            'failed_count' => $ukCertificate->rows()->where('status', UkCertificateRow::STATUS_FAILED)->count(),
        ]);

        return response()->json([
            'import_id' => $ukCertificate->id,
            'matched' => $matched,
            'unmatched' => $unmatched,
        ]);
    }

    public function finalizeImport(Request $request)
    {
        $request->validate([
            'import_id' => 'required|exists:uk_certificates,id',
        ]);

        $ukCertificate = UkCertificate::findOrFail($request->input('import_id'));
        
        // If mappings are present, process them
        $mappings = $request->input('mappings', []);
        if (!empty($mappings)) {
            // Get the original ZIP from S3
            $zipS3Path = 'uk-certificates/' . $ukCertificate->id . '/original.zip';
            $zipContent = Storage::disk('s3')->get($zipS3Path);
            $tempZipPath = storage_path('app/temp/' . $ukCertificate->id . '_finalize.zip');
            
            // Ensure temp directory exists
            if (!file_exists(dirname($tempZipPath))) {
                mkdir(dirname($tempZipPath), 0755, true);
            }
            
            file_put_contents($tempZipPath, $zipContent);
            
            // Update unmatched rows with trainee mappings
            foreach ($mappings as $mapping) {
                $row = UkCertificateRow::where('uk_certificate_id', $ukCertificate->id)
                    ->where('filename', $mapping['filename'])
                    ->first();
                
                if ($row && !$row->trainee_id) {
                    $trainee = Trainee::find($mapping['trainee_id']);
                    if ($trainee) {
                        // Upload PDF to S3 if not already uploaded
                        if (!$row->pdf_path) {
                            // Extract the PDF from the ZIP and upload to S3
                            $zip = new ZipArchive();
                            if ($zip->open($tempZipPath) === TRUE) {
                                $pdfContent = $zip->getFromName($row->filename);
                                if ($pdfContent) {
                                    $s3Path = 'uk-certificates/' . $ukCertificate->id . '/' . $row->filename;
                                    Storage::disk('s3')->put($s3Path, $pdfContent);
                                    
                                    $row->update([
                                        'pdf_path' => $s3Path,
                                    ]);
                                }
                                $zip->close();
                            }
                        }
                        
                        $row->update([
                            'trainee_id' => $trainee->id,
                            'identity_number' => $trainee->identity_number,
                        ]);
                    }
                }
            }

            // Clean up temp file
            if (file_exists($tempZipPath)) {
                unlink($tempZipPath);
            }
        }

        // Update certificate status and counts
        $ukCertificate->update([
            'status' => UkCertificate::STATUS_SENDING,
            'matched_count' => $ukCertificate->rows()->whereNotNull('trainee_id')->count(),
            'unmatched_count' => $ukCertificate->rows()->whereNull('trainee_id')->where('status', '!=', UkCertificateRow::STATUS_FAILED)->count(),
            'failed_count' => $ukCertificate->rows()->where('status', UkCertificateRow::STATUS_FAILED)->count(),
        ]);

        // Dispatch job to send emails
        dispatch(new \App\Jobs\SendUkCertificateJob($ukCertificate));

        return response()->json(['success' => true]);
    }

    public function downloadCertificate($row_id)
    {
        $row = UkCertificateRow::with(['ukCertificate.course'])->findOrFail($row_id);
        
        // Check if the user has permission to view this trainee's certificates
        if ($row->trainee_id && auth()->user()->can('view-trainee', $row->trainee)) {
            if ($row->pdf_path && Storage::disk('s3')->exists($row->pdf_path)) {
                $pdfContent = Storage::disk('s3')->get($row->pdf_path);
                
                return response($pdfContent)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="' . $row->filename . '"');
            }
        }
        
        abort(404, 'Certificate not found or access denied');
    }
}
