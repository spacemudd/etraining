<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDriveService;

class DebugGoogleDriveParsing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:google-drive {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug Google Drive URL parsing using Google Drive API';

    /**
     * Google Client instance
     */
    protected GoogleClient $googleClient;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = $this->argument('url');
        
        $this->info("🔍 Debugging Google Drive URL: {$url}");
        $this->info("Expected: Should find all 530+ certificates in the folder");
        $this->info("");
        
        try {
            // Use the exact same logic as the updated job
            $files = $this->extractFilesFromGoogleDrive($url);
            
            $this->info("📊 Results:");
            $this->info("  Total files found: " . count($files));
            $this->info("");
            
            if (empty($files)) {
                $this->error("❌ No files found!");
                return 1;
            }
            
            $this->info("📁 First 10 files extracted:");
            foreach (array_slice($files, 0, 10) as $i => $file) {
                $this->info("  " . ($i + 1) . ". " . $file['name'] . " (" . $file['size'] . ")");
            }
            
            if (count($files) > 10) {
                $this->info("  ... and " . (count($files) - 10) . " more files");
            }
            
            $this->info("");
            
            // Test filename parsing on a sample
            $this->info("🔍 Testing filename parsing on sample files:");
            $sampleFiles = array_slice($files, 0, 5);
            foreach ($sampleFiles as $file) {
                $filename = $file['name'];
                $this->info("  Testing: {$filename}");
                
                $parseResult = $this->parseGoogleDriveFilename($filename);
                if ($parseResult['valid']) {
                    $this->info("    ✅ VALID - Identity: {$parseResult['identity_number']}, Name: {$parseResult['trainee_name']}");
                } else {
                    $this->error("    ❌ INVALID - Error: {$parseResult['error']}");
                }
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Extract files from Google Drive folder using Google Drive API
     * EXACT COPY of the updated job method
     */
    private function extractFilesFromGoogleDrive($driveUrl)
    {
        try {
            $folderId = $this->extractFolderIdFromUrl($driveUrl);
            
            if (!$folderId) {
                $this->error("  ❌ Could not extract folder ID from URL");
                return [];
            }

            $this->info("  📁 Extracting files from Google Drive folder ID: {$folderId}");

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
                
                $this->info("    📄 Fetching batch " . (count($files) > 0 ? ceil(count($files) / 1000) + 1 : 1));
                
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
                
                $this->info("      ✅ Fetched " . count($fileList) . " files (Total: {$totalFiles})");
                
            } while ($pageToken);
            
            $this->info("  ✅ Successfully extracted all files from Google Drive");
            $this->info("  📊 Total files found: " . count($files));

            return $files;

        } catch (\Exception $e) {
            $this->error("  ❌ Error extracting files from Google Drive API: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Initialize Google Client with service account credentials
     * EXACT COPY of the updated job method
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
                $this->info("    🔑 Using service account credentials from file");
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
                $this->info("    🔑 Using service account credentials from environment");
            }
            
        } catch (\Exception $e) {
            $this->error("    ❌ Failed to initialize Google Client: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extract folder ID from Google Drive URL
     * EXACT COPY of the updated job method
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
     * EXACT COPY of the updated job method
     */
    private function generateDirectDownloadUrl($fileId)
    {
        return "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media";
    }

    /**
     * Format bytes to human readable format
     * EXACT COPY of the updated job method
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
     * Parse Google Drive filename using the same logic as the controller
     */
    private function parseGoogleDriveFilename($filename)
    {
        // Remove .pdf extension and trim whitespace
        $basename = trim(basename($filename, '.pdf'));
        
        // Handle files with Arabic numerals - convert to English numerals
        $basename = $this->convertArabicNumeralsToEnglish($basename);

        // Normalize NBSP and collapse runs of whitespace (spaces, tabs)
        $basename = preg_replace('/\x{00A0}+/u', ' ', $basename);
        $basename = preg_replace('/\s+/u', ' ', $basename);

        // Accept either underscores or any whitespace between ID and name
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
     * EXACT COPY of the controller method
     */
    private function convertArabicNumeralsToEnglish($text)
    {
        $arabicNumerals = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishNumerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        return str_replace($arabicNumerals, $englishNumerals, $text);
    }
}
