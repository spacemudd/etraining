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
use App\Jobs\ProcessSingleGoogleDriveFileJob;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDriveService;
use Google\Service\Exception as GoogleServiceException;

class ProcessGoogleDriveCertificatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 600; // 10 minutes - only for extracting files and dispatching jobs

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 1;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

    /**
     * The tags that should be assigned to the job.
     */
    public $tags = ['uk-certificates', 'google-drive'];

    protected $ukCertificate;
    protected $driveUrl;
    protected GoogleClient $googleClient;

    public function __construct(UkCertificate $ukCertificate, $driveUrl)
    {
        $this->ukCertificate = $ukCertificate;
        $this->driveUrl = $driveUrl;
    }

    public function handle()
    {
        try {
            Log::info('Starting Google Drive parallel processing', [
                'certificate_id' => $this->ukCertificate->id,
                'drive_url' => $this->driveUrl,
                'timestamp' => now()->toISOString()
            ]);

            // Extract files from Google Drive folder using API
            $files = $this->extractFilesFromGoogleDrive($this->driveUrl);
            
            Log::info('Files extracted for parallel processing', [
                'certificate_id' => $this->ukCertificate->id,
                'file_count' => count($files),
                'timestamp' => now()->toISOString()
            ]);
            
            if (empty($files)) {
                $this->ukCertificate->update([
                    'status' => UkCertificate::STATUS_FAILED,
                    'failed_count' => 1,
                    'progress_percentage' => 100,
                    'current_file' => 'No files found in Google Drive folder',
                ]);
                
                Log::error('No files found in Google Drive folder', [
                    'certificate_id' => $this->ukCertificate->id,
                    'drive_url' => $this->driveUrl
                ]);
                return;
            }

            $total = count($files);
            
            // Initialize counters
            $this->ukCertificate->update([
                'status' => UkCertificate::STATUS_PROCESSING,
                'total_files' => $total,
                'matched_count' => 0,
                'unmatched_count' => 0,
                'failed_count' => 0,
                'progress_percentage' => 0,
                'current_file' => 'Dispatching parallel jobs...',
            ]);

            Log::info('Dispatching parallel jobs for all files', [
                'certificate_id' => $this->ukCertificate->id,
                'total_files' => $total,
                'timestamp' => now()->toISOString()
            ]);

            // Dispatch individual jobs for each file in parallel
            foreach ($files as $index => $file) {
                Log::info('Dispatching job for file', [
                    'certificate_id' => $this->ukCertificate->id,
                    'file_index' => $index + 1,
                    'total_files' => $total,
                    'filename' => $file['name'],
                    'file_id' => $file['id'],
                    'timestamp' => now()->toISOString()
                ]);

                // Dispatch individual file processing job
                ProcessSingleGoogleDriveFileJob::dispatch($this->ukCertificate, $file, $total)
                    ->onQueue('default') // Use the same queue or specify a different one
                    ->delay(now()->addSeconds($index * 0.1)); // Small staggered delay to prevent overwhelming the API
            }

            // Update status to indicate jobs have been dispatched
            $this->ukCertificate->update([
                'current_file' => "Dispatched {$total} parallel jobs",
                'progress_percentage' => 1, // Small progress to show jobs are dispatched
            ]);
            
            Log::info('All parallel jobs dispatched successfully', [
                'certificate_id' => $this->ukCertificate->id,
                'total_jobs_dispatched' => $total,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in Google Drive parallel processing job', [
                'certificate_id' => $this->ukCertificate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);
            
            $this->ukCertificate->update([
                'status' => UkCertificate::STATUS_FAILED,
                'failed_count' => ($this->ukCertificate->failed_count ?? 0) + 1,
                'current_file' => 'Error: ' . $e->getMessage(),
            ]);
            
            throw $e;
        }
    }



    /**
     * Extract files from Google Drive folder using Google Drive API
     */
    private function extractFilesFromGoogleDrive($driveUrl)
    {
        try {
            $folderId = $this->extractFolderIdFromUrl($driveUrl);
            
            if (!$folderId) {
                Log::error('Could not extract folder ID from URL', ['url' => $driveUrl]);
                return [];
            }

            Log::info('Extracting files from Google Drive folder', [
                'folder_id' => $folderId,
                'url' => $driveUrl
            ]);

            // Initialize Google Client
            $this->initializeGoogleClient();
            
            $service = new GoogleDriveService($this->googleClient);
            
            $files = [];
            $pageToken = null;
            $totalFiles = 0;
            
            do {
                $parameters = [
                    'q' => "'{$folderId}' in parents and trashed=false and mimeType='application/pdf'",
                    'fields' => 'nextPageToken, files(id, name, size, mimeType)',
                    'pageSize' => 1000, // Maximum allowed by Google Drive API
                ];
                
                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }
                
                $results = $service->files->listFiles($parameters);
                $fileList = $results->getFiles();
                
                foreach ($fileList as $file) {
                    $fileData = [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'size' => $file->getSize() ? $this->formatBytes($file->getSize()) : 'Unknown',
                        'mime_type' => $file->getMimeType(),
                        'download_url' => $this->generateDirectDownloadUrl($file->getId())
                    ];
                    
                    Log::info('DEBUG: Extracted file from Google Drive', [
                        'certificate_id' => $this->ukCertificate->id,
                        'file_data' => $fileData,
                        'timestamp' => now()->toISOString()
                    ]);
                    
                    $files[] = $fileData;
                    $totalFiles++;
                }
                
                $pageToken = $results->getNextPageToken();
                
                Log::info('Fetched batch of files', [
                    'batch_size' => count($fileList),
                    'total_so_far' => $totalFiles,
                    'has_next_page' => !empty($pageToken)
                ]);
                
            } while ($pageToken);
            
            Log::info('Successfully extracted all files from Google Drive', [
                'folder_id' => $folderId,
                'total_files' => count($files)
            ]);

            return $files;

        } catch (\Exception $e) {
            Log::error('Error extracting files from Google Drive API', [
                'url' => $driveUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
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
                Log::info('Using service account credentials from file');
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
                Log::info('Using service account credentials from environment');
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Client', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Extract folder ID from Google Drive URL
     */
    private function extractFolderIdFromUrl($url)
    {
        // Handle different Google Drive URL formats
        $patterns = [
            '/\/folders\/([a-zA-Z0-9-_]+)/',  // Standard folder URL
            '/id=([a-zA-Z0-9-_]+)/',          // Alternative format
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }

    /**
     * Generate direct download URL for a file
     */
    private function generateDirectDownloadUrl($fileId)
    {
        return "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media";
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }


}
