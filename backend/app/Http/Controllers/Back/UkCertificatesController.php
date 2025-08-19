<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\UkCertificate;
use App\Models\Back\UkCertificateRow;
use App\Models\Back\Course;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
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
                
                // Use the enhanced filename parsing method
                $parseResult = $this->parseGoogleDriveFilename($filename);
                
                if (!$parseResult['valid']) {
                    // Invalid filename format - save to DB
                    UkCertificateRow::create([
                        'uk_certificate_id' => $ukCertificate->id,
                        'trainee_id' => null,
                        'identity_number' => $parseResult['identity_number'],
                        'trainee_name' => $parseResult['trainee_name'],
                        'filename' => $filename,
                        'status' => UkCertificateRow::STATUS_FAILED,
                        'error_message' => $parseResult['error'],
                    ]);

                    $unmatched[] = [
                        'filename' => $filename,
                        'identity_number' => $parseResult['identity_number'],
                        'trainee_name' => $parseResult['trainee_name'],
                        'reason' => $parseResult['error']
                    ];
                    continue;
                }

                $identityNumber = $parseResult['identity_number'];
                $traineeName = $parseResult['trainee_name'];

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
                        'source' => 'zip',
                        'source_ref' => 'uk-certificates/' . $ukCertificate->id . '/original.zip',
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
                                        'source' => $row->source ?? 'zip',
                                        'source_ref' => $row->source_ref ?? ('uk-certificates/' . $ukCertificate->id . '/original.zip'),
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
        
        // Check if PDF exists and download it
        if ($row->pdf_path && Storage::disk('s3')->exists($row->pdf_path)) {
            $pdfContent = Storage::disk('s3')->get($row->pdf_path);
            
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $row->filename . '"');
        }
        
        abort(404, 'Certificate not found');
    }

    /**
     * Process certificates from Google Drive folder URL
     */
    public function processGoogleDrive(Request $request)
    {
        $request->validate([
            'drive_url' => 'required|url',
            'course_id' => 'required|exists:courses,id',
        ]);

        $driveUrl = $request->input('drive_url');
        $courseId = $request->input('course_id');

        // Validate Google Drive URL format
        if (!$this->isValidGoogleDriveUrl($driveUrl)) {
            return response()->json(['error' => 'Invalid Google Drive folder URL'], 400);
        }

        // Create UK certificate record
        $ukCertificate = UkCertificate::create([
            'course_id' => $courseId,
            'status' => UkCertificate::STATUS_PROCESSING,
            'started_at' => now(),
            'drive_url' => $driveUrl,
        ]);

        // Process files in background job
        dispatch(new \App\Jobs\ProcessGoogleDriveCertificatesJob($ukCertificate, $driveUrl));

        return response()->json([
            'import_id' => $ukCertificate->id,
            'message' => 'Processing started. Files will be downloaded from Google Drive.',
            'status' => 'processing'
        ]);
    }

    /**
     * Show processing page for an import
     */
    public function showProcessing($importId)
    {
        $ukCertificate = UkCertificate::with(['course:id,name_ar', 'rows'])->findOrFail($importId);
        
        return Inertia::render('Back/UkCertificates/Processing', [
            'import' => $ukCertificate,
        ]);
    }

    /**
     * Get processing status for any import (ZIP or Google Drive)
     */
    public function getProcessingStatus($importId)
    {
        $ukCertificate = UkCertificate::with(['rows' => function ($query) {
            $query->select('uk_certificate_id', 'status', 'trainee_id', 'identity_number', 'trainee_name', 'filename', 'error_message');
        }, 'rows.trainee:id,name,email'])->findOrFail($importId);

        $matched = $ukCertificate->rows->where('trainee_id', '!=', null)->map(function ($row) {
            return [
                'id' => $row->trainee_id,
                'name' => $row->trainee->name ?? 'Unknown',
                'email' => $row->trainee->email ?? '',
                'identity_number' => $row->identity_number,
                'filename' => $row->filename,
                'status' => $row->status,
            ];
        })->values();
        
        $unmatched = $ukCertificate->rows->where('trainee_id', null)->where('status', '!=', UkCertificateRow::STATUS_FAILED)->map(function ($row) {
            return [
                'filename' => $row->filename,
                'identity_number' => $row->identity_number,
                'trainee_name' => $row->trainee_name,
                'status' => $row->status,
                'searchQuery' => '',
                'searchResults' => [],
                'selectedTrainee' => null,
            ];
        })->values();
        
        $failed = $ukCertificate->rows->where('status', UkCertificateRow::STATUS_FAILED)->map(function ($row) {
            return [
                'filename' => $row->filename,
                'identity_number' => $row->identity_number,
                'trainee_name' => $row->trainee_name,
                'error_message' => $row->error_message,
                'status' => $row->status,
            ];
        })->values();

        return response()->json([
            'import_id' => $ukCertificate->id,
            'status' => $ukCertificate->status,
            'progress_percentage' => $ukCertificate->progress_percentage ?? 0,
            'current_file' => $ukCertificate->current_file ?? '',
            'total_files' => $ukCertificate->total_files ?? 0,
            'matched_count' => $ukCertificate->matched_count ?? 0,
            'unmatched_count' => $ukCertificate->unmatched_count ?? 0,
            'failed_count' => $ukCertificate->failed_count ?? 0,
            'started_at' => $ukCertificate->started_at,
            'completed_at' => $ukCertificate->completed_at,
            'drive_url' => $ukCertificate->drive_url,
            'course_name' => $ukCertificate->course->name_ar ?? 'Unknown Course',
            'matched' => $matched,
            'unmatched' => $unmatched,
            'failed' => $failed,
        ]);
    }

    /**
     * Enhanced filename parsing for Google Drive files
     */
    public function parseGoogleDriveFilename($filename)
    {
        // Remove .pdf extension and trim whitespace
        $basename = trim(basename($filename, '.pdf'));
        
        // Handle files with Arabic numerals - convert to English numerals
        $basename = $this->convertArabicNumeralsToEnglish($basename);
        
        // Normalize NBSP and collapse runs of whitespace (spaces, tabs)
        $basename = preg_replace('/\x{00A0}+/u', ' ', $basename);
        $basename = preg_replace('/\s+/u', ' ', $basename);

        // Accept either underscores or any whitespace between ID and name
        // Examples: "10000000 Name", "10000000\tName", "10000000   Name", "10000000_Name"
        if (preg_match('/^\s*([0-9]+)[\s_]+(.+?)\s*$/u', $basename, $matches) !== 1) {
            return [
                'valid' => false,
                'identity_number' => '',
                'trainee_name' => '',
                'error' => 'Invalid filename format. Expected: {identity_number} {name}.pdf or {identity_number}_{name}.pdf'
            ];
        }

        $identityNumber = trim($matches[1]);
        $traineeName = trim($matches[2]);
        
        // Additional validation
        if (empty($identityNumber) || empty($traineeName)) {
            return [
                'valid' => false,
                'identity_number' => $identityNumber,
                'trainee_name' => $traineeName,
                'error' => 'Identity number or trainee name is empty'
            ];
        }
        
        // Validate identity number format (should be numeric)
        if (!is_numeric($identityNumber)) {
            return [
                'valid' => false,
                'identity_number' => $identityNumber,
                'trainee_name' => $traineeName,
                'error' => 'Identity number must be numeric'
            ];
        }
        
        return [
            'valid' => true,
            'identity_number' => $identityNumber,
            'trainee_name' => $traineeName,
            'error' => null
        ];
    }

    /**
     * Convert Arabic numerals to English numerals
     */
    private function convertArabicNumeralsToEnglish($text)
    {
        $arabicNumerals = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishNumerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        return str_replace($arabicNumerals, $englishNumerals, $text);
    }

    /**
     * Validate Google Drive URL format
     */
    public function isValidGoogleDriveUrl($url)
    {
        return preg_match('/drive\.google\.com\/drive\/folders\/[a-zA-Z0-9-_]+/', $url);
    }

    /**
     * Extract folder ID from Google Drive URL
     */
    public function extractFolderIdFromUrl($url)
    {
        if (preg_match('/\/folders\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Create failed row record
     */
    public function createFailedRow($ukCertificate, $filename, $identityNumber, $traineeName, $errorMessage)
    {
        UkCertificateRow::create([
            'uk_certificate_id' => $ukCertificate->id,
            'trainee_id' => null,
            'identity_number' => $identityNumber,
            'trainee_name' => $traineeName,
            'filename' => $filename,
            'status' => UkCertificateRow::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }
}
