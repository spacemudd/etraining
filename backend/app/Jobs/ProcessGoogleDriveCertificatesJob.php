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

class ProcessGoogleDriveCertificatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 1;

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
                    Log::info('DEBUG: Starting to process file in main loop', [
                        'certificate_id' => $this->ukCertificate->id,
                        'filename' => $file['name'],
                        'file_data' => $file,
                        'processed' => $processed,
                        'total' => $total,
                        'timestamp' => now()->toISOString()
                    ]);
                    
                    // Update progress
                    $this->ukCertificate->update([
                        'progress_percentage' => ($processed / $total) * 100,
                        'current_file' => $file['name'],
                    ]);

                    $result = $this->processFile($file);
                    
                    Log::info('DEBUG: File processing result', [
                        'certificate_id' => $this->ukCertificate->id,
                        'filename' => $file['name'],
                        'result' => $result,
                        'timestamp' => now()->toISOString()
                    ]);
                    
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
                    Log::error('DEBUG: Exception in main file processing loop', [
                        'certificate_id' => $this->ukCertificate->id,
                        'filename' => $file['name'],
                        'file_data' => $file,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'timestamp' => now()->toISOString()
                    ]);
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

    /**
     * Process individual file
     */
    private function processFile($file)
    {
        $filename = $file['name'];
        
        Log::info('DEBUG: Processing file in Google Drive job', [
            'certificate_id' => $this->ukCertificate->id,
            'filename' => $filename,
            'file_data' => $file,
            'timestamp' => now()->toISOString()
        ]);
        
        // Parse filename using the controller method
        $controller = app(\App\Http\Controllers\Back\UkCertificatesController::class);
        $parseResult = $controller->parseGoogleDriveFilename($filename);
        
        Log::info('DEBUG: Filename parsing result', [
            'certificate_id' => $this->ukCertificate->id,
            'filename' => $filename,
            'parse_result' => $parseResult,
            'timestamp' => now()->toISOString()
        ]);
        
        if (!$parseResult['valid']) {
            Log::warning('DEBUG: Invalid filename format', [
                'certificate_id' => $this->ukCertificate->id,
                'filename' => $filename,
                'error' => $parseResult['error'],
                'timestamp' => now()->toISOString()
            ]);
            $controller->createFailedRow($this->ukCertificate, $filename, '', '', $parseResult['error']);
            return 'failed';
        }

        $identityNumber = $parseResult['identity_number'];
        $traineeName = $parseResult['trainee_name'];

        // Try to find trainee by identity number
        $trainee = Trainee::where('identity_number', $identityNumber)->first();
        
        Log::info('DEBUG: Trainee lookup result', [
            'certificate_id' => $this->ukCertificate->id,
            'filename' => $filename,
            'identity_number' => $identityNumber,
            'trainee_name' => $traineeName,
            'trainee_found' => $trainee ? true : false,
            'trainee_id' => $trainee ? $trainee->id : null,
            'timestamp' => now()->toISOString()
        ]);

        if ($trainee) {
            try {
                $s3Path = 'uk-certificates/' . $this->ukCertificate->id . '/' . $filename;
                
                Log::info('DEBUG: Starting file download for matched trainee', [
                    'certificate_id' => $this->ukCertificate->id,
                    'filename' => $filename,
                    'trainee_id' => $trainee->id,
                    'download_url' => $file['download_url'],
                    's3_path' => $s3Path,
                    'timestamp' => now()->toISOString()
                ]);
                
                // Actually download the file from Google Drive and upload to S3
                $this->downloadAndUploadFile($file['download_url'], $s3Path);
                
                Log::info('DEBUG: File successfully downloaded and uploaded to S3', [
                    'certificate_id' => $this->ukCertificate->id,
                    'filename' => $filename,
                    'trainee_id' => $trainee->id,
                    's3_path' => $s3Path,
                    'timestamp' => now()->toISOString()
                ]);

                // Create row record
                $row = UkCertificateRow::create([
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
                
                Log::info('DEBUG: Created matched row record', [
                    'certificate_id' => $this->ukCertificate->id,
                    'row_id' => $row->id,
                    'filename' => $filename,
                    'trainee_id' => $trainee->id,
                    'pdf_path' => $s3Path,
                    'source' => 'gdrive',
                    'source_ref' => $file['id'] ?? null,
                    'timestamp' => now()->toISOString()
                ]);

                return 'matched';
            } catch (\Exception $e) {
                Log::error('DEBUG: Failed to download/upload file for matched trainee', [
                    'certificate_id' => $this->ukCertificate->id,
                    'filename' => $filename,
                    'trainee_id' => $trainee->id,
                    'download_url' => $file['download_url'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'timestamp' => now()->toISOString()
                ]);
                $controller->createFailedRow($this->ukCertificate, $filename, $identityNumber, $traineeName, 'Download error: ' . $e->getMessage());
                return 'failed';
            }
        } else {
            Log::info('DEBUG: Creating unmatched row record', [
                'certificate_id' => $this->ukCertificate->id,
                'filename' => $filename,
                'identity_number' => $identityNumber,
                'trainee_name' => $traineeName,
                'source' => 'gdrive',
                'source_ref' => $file['id'] ?? null,
                'timestamp' => now()->toISOString()
            ]);
            
            // Create unmatched row record with source info but no pdf_path yet
            $row = UkCertificateRow::create([
                'uk_certificate_id' => $this->ukCertificate->id,
                'trainee_id' => null,
                'identity_number' => $identityNumber,
                'trainee_name' => $traineeName,
                'filename' => $filename,
                'source' => 'gdrive',
                'source_ref' => $file['id'] ?? null,
                'status' => UkCertificateRow::STATUS_PENDING,
            ]);
            
            Log::info('DEBUG: Created unmatched row record', [
                'certificate_id' => $this->ukCertificate->id,
                'row_id' => $row->id,
                'filename' => $filename,
                'source' => 'gdrive',
                'source_ref' => $file['id'] ?? null,
                'timestamp' => now()->toISOString()
            ]);

            return 'unmatched';
        }
    }

    /**
     * Download file from Google Drive and upload to S3
     */
    private function downloadAndUploadFile($downloadUrl, $s3Path)
    {
        Log::info('DEBUG: Starting downloadAndUploadFile', [
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
            
            Log::info('DEBUG: Using authenticated Google Drive service to download file', [
                'certificate_id' => $this->ukCertificate->id,
                'file_id' => $fileId,
                's3_path' => $s3Path,
                'timestamp' => now()->toISOString()
            ]);
            
            // Verify Google Client is initialized
            if (!$this->googleClient) {
                throw new \Exception('Google Client not initialized');
            }
            
            // Use the authenticated Google Drive service to download the file
            $service = new GoogleDriveService($this->googleClient);
            
            try {
                $file = $service->files->get($fileId, ['alt' => 'media']);
            } catch (GoogleServiceException $e) {
                Log::error('DEBUG: Google Drive API error', [
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
            
            Log::info('DEBUG: File downloaded successfully from Google Drive', [
                'certificate_id' => $this->ukCertificate->id,
                'file_id' => $fileId,
                's3_path' => $s3Path,
                'content_size' => strlen($fileContent),
                'timestamp' => now()->toISOString()
            ]);
            
            // Upload to S3
            Storage::disk('s3')->put($s3Path, $fileContent);
            
            Log::info('DEBUG: File successfully uploaded to S3', [
                'certificate_id' => $this->ukCertificate->id,
                'file_id' => $fileId,
                's3_path' => $s3Path,
                's3_exists' => Storage::disk('s3')->exists($s3Path),
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('DEBUG: Exception in downloadAndUploadFile', [
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
