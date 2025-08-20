<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Back\UkCertificate;
use App\Models\Back\UkCertificateRow;
use App\Models\Back\Trainee;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDriveService;
use Google\Service\Exception as GoogleServiceException;

class ProcessSingleGoogleDriveFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 300; // 5 minutes per file

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [30, 60, 120]; // Exponential backoff

    /**
     * The tags that should be assigned to the job.
     */
    public $tags = ['uk-certificates', 'google-drive', 'single-file'];

    protected $ukCertificate;
    protected $fileData;
    protected $totalFiles;
    protected GoogleClient $googleClient;

    public function __construct(UkCertificate $ukCertificate, array $fileData, int $totalFiles)
    {
        $this->ukCertificate = $ukCertificate;
        $this->fileData = $fileData;
        $this->totalFiles = $totalFiles;
    }

    public function handle()
    {
        try {
            $filename = $this->fileData['name'];
            
            Log::info('Processing single file from Google Drive', [
                'certificate_id' => $this->ukCertificate->id,
                'filename' => $filename,
                'file_id' => $this->fileData['id'],
                'file_size' => $this->fileData['size'],
                'timestamp' => now()->toISOString()
            ]);

            // Initialize Google Client
            $this->initializeGoogleClient();

            // Sanitize the filename for safe file system usage
            $sanitizedFilename = $this->sanitizeFilename($filename);

            // Parse filename using the controller method
            $controller = app(\App\Http\Controllers\Back\UkCertificatesController::class);
            $parseResult = $controller->parseGoogleDriveFilename($sanitizedFilename);

            if (!$parseResult['valid']) {
                Log::warning('Invalid filename format for single file', [
                    'certificate_id' => $this->ukCertificate->id,
                    'original_filename' => $filename,
                    'sanitized_filename' => $sanitizedFilename,
                    'error' => $parseResult['error'],
                    'timestamp' => now()->toISOString()
                ]);
                
                $controller->createFailedRow($this->ukCertificate, $sanitizedFilename, '', '', $parseResult['error']);
                $this->updateProgress('failed');
                return;
            }

            $identityNumber = $parseResult['identity_number'];
            $traineeName = $parseResult['trainee_name'];

            // Try to find trainee by identity number (including trashed)
            $trainee = Trainee::withTrashed()->where('identity_number', $identityNumber)->first();

            if ($trainee) {
                // Process matched trainee
                $this->processMatchedTrainee($trainee, $sanitizedFilename, $identityNumber, $traineeName);
                $this->updateProgress('matched');
            } else {
                // Process unmatched trainee
                $this->processUnmatchedTrainee($sanitizedFilename, $identityNumber, $traineeName);
                $this->updateProgress('unmatched');
            }

        } catch (\Exception $e) {
            Log::error('Error processing single Google Drive file', [
                'certificate_id' => $this->ukCertificate->id,
                'filename' => $this->fileData['name'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            $this->updateProgress('failed');
            throw $e;
        }
    }

    /**
     * Process matched trainee
     */
    private function processMatchedTrainee($trainee, $sanitizedFilename, $identityNumber, $traineeName)
    {
        try {
            $s3Path = 'uk-certificates/' . $this->ukCertificate->id . '/' . $sanitizedFilename;

            Log::info('Starting file download for matched trainee', [
                'certificate_id' => $this->ukCertificate->id,
                'original_filename' => $this->fileData['name'],
                'sanitized_filename' => $sanitizedFilename,
                'trainee_id' => $trainee->id,
                'download_url' => $this->fileData['download_url'],
                's3_path' => $s3Path,
                'timestamp' => now()->toISOString()
            ]);

            // Download and upload file
            $this->downloadAndUploadFile($this->fileData['download_url'], $s3Path);

            // Create row record with sanitized filename
            $row = UkCertificateRow::create([
                'uk_certificate_id' => $this->ukCertificate->id,
                'trainee_id' => $trainee->id,
                'identity_number' => $identityNumber,
                'trainee_name' => $traineeName,
                'filename' => $sanitizedFilename,
                'pdf_path' => $s3Path,
                'source' => 'gdrive',
                'source_ref' => $this->fileData['id'] ?? null,
                'status' => UkCertificateRow::STATUS_PENDING,
            ]);

            Log::info('Created matched row record for single file', [
                'certificate_id' => $this->ukCertificate->id,
                'row_id' => $row->id,
                'original_filename' => $this->fileData['name'],
                'sanitized_filename' => $sanitizedFilename,
                'trainee_id' => $trainee->id,
                'pdf_path' => $s3Path,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process matched trainee for single file', [
                'certificate_id' => $this->ukCertificate->id,
                'original_filename' => $this->fileData['name'],
                'sanitized_filename' => $sanitizedFilename,
                'trainee_id' => $trainee->id,
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ]);

            $controller = app(\App\Http\Controllers\Back\UkCertificatesController::class);
            $controller->createFailedRow($this->ukCertificate, $sanitizedFilename, $identityNumber, $traineeName, 'Download error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process unmatched trainee
     */
    private function processUnmatchedTrainee($sanitizedFilename, $identityNumber, $traineeName)
    {
        Log::info('Creating unmatched row record for single file', [
            'certificate_id' => $this->ukCertificate->id,
            'original_filename' => $this->fileData['name'],
            'sanitized_filename' => $sanitizedFilename,
            'identity_number' => $identityNumber,
            'trainee_name' => $traineeName,
            'timestamp' => now()->toISOString()
        ]);

        // Create unmatched row record with sanitized filename
        $row = UkCertificateRow::create([
            'uk_certificate_id' => $this->ukCertificate->id,
            'trainee_id' => null,
            'identity_number' => $identityNumber,
            'trainee_name' => $traineeName,
            'filename' => $sanitizedFilename,
            'source' => 'gdrive',
            'source_ref' => $this->fileData['id'] ?? null,
            'status' => UkCertificateRow::STATUS_PENDING,
        ]);

        Log::info('Created unmatched row record for single file', [
            'certificate_id' => $this->ukCertificate->id,
            'row_id' => $row->id,
            'original_filename' => $this->fileData['name'],
            'sanitized_filename' => $sanitizedFilename,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Update overall progress
     */
    private function updateProgress($result)
    {
        // Use atomic increment to safely update counters across parallel jobs
        $certificate = UkCertificate::find($this->ukCertificate->id);
        
        if ($certificate) {
            $field = $result . '_count';
            $certificate->increment($field);
            
            // Calculate progress based on total processed files
            $totalProcessed = ($certificate->matched_count ?? 0) + 
                            ($certificate->unmatched_count ?? 0) + 
                            ($certificate->failed_count ?? 0);
            
            $progressPercentage = ($totalProcessed / $this->totalFiles) * 100;
            
            $certificate->update([
                'progress_percentage' => min(100, $progressPercentage),
                'current_file' => "Processing files in parallel ({$totalProcessed}/{$this->totalFiles})",
            ]);

            // Check if all files are processed
            if ($totalProcessed >= $this->totalFiles) {
                $certificate->update([
                    'status' => UkCertificate::STATUS_COMPLETED,
                    'progress_percentage' => 100,
                    'current_file' => 'All files processed',
                    'total_files' => $this->totalFiles,
                    'completed_at' => now(),
                ]);

                Log::info('All parallel jobs completed', [
                    'certificate_id' => $this->ukCertificate->id,
                    'total_files' => $this->totalFiles,
                    'matched' => $certificate->matched_count ?? 0,
                    'unmatched' => $certificate->unmatched_count ?? 0,
                    'failed' => $certificate->failed_count ?? 0,
                    'timestamp' => now()->toISOString()
                ]);
            }
        }
    }

    /**
     * Sanitize filename for safe file system usage
     */
    private function sanitizeFilename($filename)
    {
        $original = $filename;
        
        // Replace tab characters with spaces
        $sanitized = str_replace("\t", " ", $filename);
        
        // Remove any other control characters that might cause issues
        $sanitized = preg_replace('/[\x00-\x1F\x7F]/', '', $sanitized);
        
        // Ensure the filename is not empty after sanitization
        if (empty(trim($sanitized))) {
            throw new \Exception('Filename is empty after sanitization');
        }
        
        // Log the sanitization process if there were changes
        if ($original !== $sanitized) {
            Log::info('Filename sanitized in single file job', [
                'certificate_id' => $this->ukCertificate->id,
                'original_filename' => $original,
                'sanitized_filename' => $sanitized,
                'had_tabs' => strpos($original, "\t") !== false,
                'had_control_chars' => preg_match('/[\x00-\x1F\x7F]/', $original),
                'timestamp' => now()->toISOString()
            ]);
        }
        
        return $sanitized;
    }

    /**
     * Initialize Google Client with service account credentials
     */
    private function initializeGoogleClient()
    {
        try {
            $this->googleClient = new GoogleClient();
            $this->googleClient->setApplicationName('ETraining UK Certificates');
            $this->googleClient->setScopes([GoogleDriveService::DRIVE_READONLY]);
            
            // Check if we have service account credentials
            $credentialsPath = storage_path('app/google-drive-credentials.json');
            
            if (file_exists($credentialsPath)) {
                $this->googleClient->setAuthConfig($credentialsPath);
                Log::info('Using service account credentials from file for single file job');
            } else {
                // Fallback to environment variables
                $this->googleClient->setAuthConfig([
                    'type' => 'service_account',
                    'project_id' => config('services.google.project_id'),
                    'private_key_id' => config('services.google.private_key_id'),
                    'private_key' => config('services.google.private_key'),
                    'client_email' => config('services.google.client_email'),
                    'client_id' => config('services.google.client_id'),
                    'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
                    'token_uri' => 'https://oauth2.googleapis.com/token',
                    'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
                    'client_x509_cert_url' => config('services.google.client_x509_cert_url'),
                ]);
                Log::info('Using service account credentials from environment for single file job');
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Client for single file job', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Download file from Google Drive and upload to S3
     */
    private function downloadAndUploadFile($downloadUrl, $s3Path)
    {
        Log::info('Starting downloadAndUploadFile for single file', [
            'certificate_id' => $this->ukCertificate->id,
            'download_url' => $downloadUrl,
            's3_path' => $s3Path,
            'timestamp' => now()->toISOString()
        ]);
        
        try {
            // Extract file ID from the download URL
            $fileId = $this->extractFileIdFromUrl($downloadUrl);
            
            if (!$fileId) {
                throw new \Exception('Could not extract file ID from download URL: ' . $downloadUrl);
            }
            
            // Verify Google Client is initialized
            if (!$this->googleClient) {
                throw new \Exception('Google Client not initialized');
            }
            
            // Use the authenticated Google Drive service to download the file
            $service = new GoogleDriveService($this->googleClient);
            
            try {
                $file = $service->files->get($fileId, ['alt' => 'media']);
            } catch (GoogleServiceException $e) {
                Log::error('Google Drive API error in single file job', [
                    'certificate_id' => $this->ukCertificate->id,
                    'file_id' => $fileId,
                    'error_code' => $e->getCode(),
                    'error_message' => $e->getMessage(),
                    'timestamp' => now()->toISOString()
                ]);
                
                if ($e->getCode() == 403) {
                    throw new \Exception('Access denied to file. Service account may not have permission to access this file or the file may not exist.');
                } elseif ($e->getCode() == 404) {
                    throw new \Exception('File not found. The file may have been deleted or moved.');
                } else {
                    throw new \Exception('Google Drive API error: ' . $e->getMessage());
                }
            }
            
            // Get the file content
            $fileContent = $file->getBody()->getContents();
            
            Log::info('File downloaded successfully from Google Drive in single file job', [
                'certificate_id' => $this->ukCertificate->id,
                'file_id' => $fileId,
                's3_path' => $s3Path,
                'content_size' => strlen($fileContent),
                'timestamp' => now()->toISOString()
            ]);
            
            // Upload to S3
            Storage::disk('s3')->put($s3Path, $fileContent);
            
            Log::info('File successfully uploaded to S3 in single file job', [
                'certificate_id' => $this->ukCertificate->id,
                'file_id' => $fileId,
                's3_path' => $s3Path,
                's3_exists' => Storage::disk('s3')->exists($s3Path),
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception in downloadAndUploadFile for single file job', [
                'certificate_id' => $this->ukCertificate->id,
                'download_url' => $downloadUrl,
                's3_path' => $s3Path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);
            throw $e;
        }
    }

    /**
     * Extract file ID from Google Drive download URL
     */
    private function extractFileIdFromUrl($downloadUrl)
    {
        // Extract file ID from URL like: https://www.googleapis.com/drive/v3/files/{fileId}?alt=media
        if (preg_match('/\/files\/([^?]+)/', $downloadUrl, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
