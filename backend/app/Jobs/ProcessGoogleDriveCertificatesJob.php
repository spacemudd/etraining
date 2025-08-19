<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\Back\UkCertificate;
use App\Models\Back\UkCertificateRow;
use App\Models\Back\Trainee;
use Google_Client;
use Google_Service_Drive;

class ProcessGoogleDriveCertificatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ukCertificate;
    protected $driveUrl;
    protected $googleClient;

    public function __construct(UkCertificate $ukCertificate, $driveUrl)
    {
        $this->ukCertificate = $ukCertificate;
        $this->driveUrl = $driveUrl;
    }

    public function handle()
    {
        try {
            Log::info('CRITICAL DEBUG: Job handle method started with UPDATED CODE', [
                'certificate_id' => $this->ukCertificate->id,
                'drive_url' => $this->driveUrl,
                'timestamp' => now()->toISOString()
            ]);
            
            Log::info('Starting Google Drive processing', [
                'certificate_id' => $this->ukCertificate->id,
                'drive_url' => $this->driveUrl
            ]);

            // Extract files from Google Drive folder using API
            $files = $this->extractFilesFromGoogleDrive($this->driveUrl);
            
            Log::info('DEBUG: Files extracted in job handle method', [
                'certificate_id' => $this->ukCertificate->id,
                'file_count' => count($files),
                'filenames' => array_column($files, 'name')
            ]);
            
            if (empty($files)) {
                $this->ukCertificate->update([
                    'status' => UkCertificate::STATUS_FAILED,
                    'failed_count' => 1,
                    'progress_percentage' => 100,
                ]);
                
                Log::error('No files found in Google Drive folder', [
                    'certificate_id' => $this->ukCertificate->id,
                    'drive_url' => $this->driveUrl
                ]);
                return;
            }

            $matched = 0;
            $unmatched = 0;
            $failed = 0;
            $processed = 0;
            $total = count($files);

            Log::info('Processing files from Google Drive', [
                'certificate_id' => $this->ukCertificate->id,
                'total_files' => $total
            ]);

            foreach ($files as $file) {
                try {
                    // Update progress
                    $this->ukCertificate->update([
                        'progress_percentage' => ($processed / $total) * 100,
                        'current_file' => $file['name'],
                    ]);

                    $result = $this->processFile($file);
                    
                    switch ($result) {
                        case 'matched':
                            $matched++;
                            break;
                        case 'unmatched':
                            $unmatched++;
                            break;
                        case 'failed':
                            $failed++;
                            break;
                    }

                    $processed++;
                    
                    // Small delay to prevent overwhelming Google Drive
                    usleep(200000); // 0.2 seconds

                } catch (\Exception $e) {
                    Log::error('Error processing file', [
                        'certificate_id' => $this->ukCertificate->id,
                        'filename' => $file['name'],
                        'error' => $e->getMessage()
                    ]);
                    $failed++;
                    $processed++;
                }
            }

            // Update final counts
            $this->ukCertificate->update([
                'total_files' => $total,
                'matched_count' => $matched,
                'unmatched_count' => $unmatched,
                'failed_count' => $failed,
                'status' => UkCertificate::STATUS_COMPLETED,
                'progress_percentage' => 100,
                'completed_at' => now(),
            ]);

            Log::info('Google Drive processing completed', [
                'certificate_id' => $this->ukCertificate->id,
                'total' => $total,
                'matched' => $matched,
                'unmatched' => $unmatched,
                'failed' => $failed
            ]);

        } catch (\Exception $e) {
            Log::error('Google Drive processing failed', [
                'certificate_id' => $this->ukCertificate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->ukCertificate->update([
                'status' => UkCertificate::STATUS_FAILED,
                'progress_percentage' => 100,
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
            
            $service = new Google_Service_Drive($this->googleClient);
            
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
                    $files[] = [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'size' => $file->getSize() ? $this->formatBytes($file->getSize()) : 'Unknown',
                        'mime_type' => $file->getMimeType(),
                        'download_url' => $this->generateDirectDownloadUrl($file->getId())
                    ];
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
            $this->googleClient = new Google_Client();
            $this->googleClient->setApplicationName('ETraining UK Certificates');
            $this->googleClient->setScopes([Google_Service_Drive::DRIVE_READONLY]);
            
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

    /**
     * Process individual file
     */
    private function processFile($file)
    {
        $filename = $file['name'];
        
        // Parse filename using the controller method
        $controller = app(\App\Http\Controllers\Back\UkCertificatesController::class);
        $parseResult = $controller->parseGoogleDriveFilename($filename);
        
        if (!$parseResult['valid']) {
            $controller->createFailedRow($this->ukCertificate, $filename, '', '', $parseResult['error']);
            return 'failed';
        }

        $identityNumber = $parseResult['identity_number'];
        $traineeName = $parseResult['trainee_name'];

        // Try to find trainee by identity number
        $trainee = Trainee::where('identity_number', $identityNumber)->first();

        if ($trainee) {
            try {
                // For now, we'll create the record without actually downloading the file
                // In production, you'd download from $file['download_url'] and upload to S3
                $s3Path = 'uk-certificates/' . $this->ukCertificate->id . '/' . $filename;
                
                // Simulate file download and upload (placeholder)
                // $this->downloadAndUploadFile($file['download_url'], $s3Path);

                // Create row record
                UkCertificateRow::create([
                    'uk_certificate_id' => $this->ukCertificate->id,
                    'trainee_id' => $trainee->id,
                    'identity_number' => $identityNumber,
                    'trainee_name' => $traineeName,
                    'filename' => $filename,
                    'pdf_path' => $s3Path,
                    'source' => 'gdrive',
                    'source_ref' => $file['id'] ?? null,
                    'status' => UkCertificateRow::STATUS_PENDING,
                ]);

                return 'matched';
            } catch (\Exception $e) {
                $controller->createFailedRow($this->ukCertificate, $filename, $identityNumber, $traineeName, 'Download error: ' . $e->getMessage());
                return 'failed';
            }
        } else {
            // Create unmatched row record
            UkCertificateRow::create([
                'uk_certificate_id' => $this->ukCertificate->id,
                'trainee_id' => null,
                'identity_number' => $identityNumber,
                'trainee_name' => $traineeName,
                'filename' => $filename,
                'status' => UkCertificateRow::STATUS_PENDING,
            ]);

            return 'unmatched';
        }
    }

    /**
     * Download file from Google Drive and upload to S3
     */
    private function downloadAndUploadFile($downloadUrl, $s3Path)
    {
        // Download file with streaming
        $response = Http::timeout(300)->get($downloadUrl);
        
        if ($response->successful()) {
            Storage::disk('s3')->put($s3Path, $response->body());
        } else {
            throw new \Exception('Failed to download file from Google Drive');
        }
    }
}
