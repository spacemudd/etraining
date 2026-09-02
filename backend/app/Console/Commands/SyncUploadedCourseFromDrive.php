<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDriveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SyncUploadedCourseFromDrive extends Command
{
    protected $signature = 'uploaded-courses:sync
                            {courseId=1 : Uploaded course ID from config}
                            {--folder= : Override Google Drive folder ID}
                            {--force : Re-download files even if they already exist}';

    protected $description = 'Recursively sync an uploaded course from Google Drive onto local storage';

    protected GoogleClient $googleClient;

    public function handle(): int
    {
        $courseId = (int) $this->argument('courseId');
        $courses = config('uploaded-courses', []);

        if (!isset($courses[$courseId])) {
            $this->error("Course ID {$courseId} is not defined in config/uploaded-courses.php");

            return 1;
        }

        $course = $courses[$courseId];
        $folderId = $this->option('folder') ?: ($course['drive_folder_id'] ?? null);

        if (!$folderId) {
            $this->error('No Google Drive folder ID provided.');

            return 1;
        }

        $destRoot = storage_path("app/uploaded-courses/{$courseId}");
        if (!is_dir($destRoot)) {
            File::makeDirectory($destRoot, 0755, true);
        }

        $this->info("Syncing course {$courseId}: {$course['title']}");
        $this->info("Drive folder: {$folderId}");
        $this->info("Destination: {$destRoot}");

        try {
            $this->initializeGoogleClient();
            $service = new GoogleDriveService($this->googleClient);

            $files = $this->listFilesRecursive($service, $folderId);
            $this->info('Found ' . count($files) . ' file(s) to sync.');

            $downloaded = 0;
            $skipped = 0;
            $failed = 0;

            foreach ($files as $index => $file) {
                $relativePath = $file['relative_path'];
                $localPath = $destRoot . DIRECTORY_SEPARATOR . $relativePath;
                $localDir = dirname($localPath);
                if (!is_dir($localDir)) {
                    File::makeDirectory($localDir, 0755, true);
                }

                $expectedSize = (int) ($file['size'] ?? 0);
                $label = ($index + 1) . '/' . count($files) . ' ' . $relativePath;

                if (
                    !$this->option('force')
                    && is_file($localPath)
                    && ($expectedSize === 0 || filesize($localPath) === $expectedSize)
                ) {
                    $this->line("  skip  {$label}");
                    $skipped++;
                    continue;
                }

                $this->line("  get   {$label} (" . $this->formatBytes($expectedSize) . ')');

                try {
                    $this->downloadFile($service, $file['id'], $localPath);
                    $downloaded++;
                } catch (\Throwable $e) {
                    $failed++;
                    $this->error("  fail  {$relativePath}: {$e->getMessage()}");
                    Log::error('uploaded-courses sync failed for file', [
                        'course_id' => $courseId,
                        'file_id' => $file['id'],
                        'path' => $relativePath,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $manifest = $this->buildManifest($course, $files, $destRoot);
            $manifestPath = $destRoot . DIRECTORY_SEPARATOR . 'manifest.json';
            File::put($manifestPath, json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            $this->info("Manifest written: {$manifestPath}");
            $this->info("Done. downloaded={$downloaded} skipped={$skipped} failed={$failed}");

            return $failed > 0 ? 1 : 0;
        } catch (\Throwable $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            Log::error('uploaded-courses sync failed', [
                'course_id' => $courseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 1;
        }
    }

    /**
     * @return array<int, array{id: string, name: string, relative_path: string, mime_type: string, size: int}>
     */
    private function listFilesRecursive(GoogleDriveService $service, string $folderId, string $prefix = ''): array
    {
        $files = [];
        $pageToken = null;

        do {
            $params = [
                'q' => "'{$folderId}' in parents and trashed=false",
                'fields' => 'nextPageToken, files(id, name, size, mimeType)',
                'pageSize' => 1000,
                'orderBy' => 'name',
            ];

            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            $results = $service->files->listFiles($params);

            foreach ($results->getFiles() as $file) {
                $name = $file->getName();
                $mime = $file->getMimeType();
                $relative = ltrim($prefix . '/' . $name, '/');

                if ($mime === 'application/vnd.google-apps.folder') {
                    $files = array_merge(
                        $files,
                        $this->listFilesRecursive($service, $file->getId(), $relative)
                    );
                    continue;
                }

                // Skip Google Docs/Sheets native files (not downloadable as binary without export)
                if (strpos($mime, 'application/vnd.google-apps.') === 0) {
                    $this->warn("  skip google-native: {$relative}");
                    continue;
                }

                $files[] = [
                    'id' => $file->getId(),
                    'name' => $name,
                    'relative_path' => $relative,
                    'mime_type' => $mime,
                    'size' => (int) ($file->getSize() ?? 0),
                ];
            }

            $pageToken = $results->getNextPageToken();
        } while ($pageToken);

        return $files;
    }

    private function downloadFile(GoogleDriveService $service, string $fileId, string $localPath): void
    {
        $tmpPath = $localPath . '.part';

        if (is_file($tmpPath)) {
            @unlink($tmpPath);
        }

        $response = $service->files->get($fileId, ['alt' => 'media']);
        $body = $response->getBody();
        $handle = fopen($tmpPath, 'wb');

        if ($handle === false) {
            throw new \RuntimeException("Cannot open temp file for writing: {$tmpPath}");
        }

        try {
            while (!$body->eof()) {
                $chunk = $body->read(1024 * 1024);
                if ($chunk === '') {
                    break;
                }
                fwrite($handle, $chunk);
            }
        } finally {
            fclose($handle);
        }

        if (!rename($tmpPath, $localPath)) {
            throw new \RuntimeException("Failed to move downloaded file to {$localPath}");
        }
    }

    /**
     * @param  array<int, array{id: string, name: string, relative_path: string, mime_type: string, size: int}>  $files
     * @return array<string, mixed>
     */
    private function buildManifest(array $course, array $files, string $destRoot): array
    {
        $units = [];

        foreach ($files as $file) {
            $parts = explode('/', $file['relative_path']);
            $unitName = $parts[0] ?? 'عام';
            $sessionName = $parts[1] ?? 'جلسات';
            $lessonName = $file['name'];

            if (!isset($units[$unitName])) {
                $units[$unitName] = [
                    'title' => $unitName,
                    'sessions' => [],
                ];
            }

            if (!isset($units[$unitName]['sessions'][$sessionName])) {
                $units[$unitName]['sessions'][$sessionName] = [
                    'title' => $sessionName,
                    'lessons' => [],
                ];
            }

            $units[$unitName]['sessions'][$sessionName]['lessons'][] = [
                'title' => pathinfo($lessonName, PATHINFO_FILENAME),
                'filename' => $lessonName,
                'path' => $file['relative_path'],
                'mime_type' => $file['mime_type'],
                'size' => $file['size'],
                'size_label' => $this->formatBytes($file['size']),
                'exists' => is_file($destRoot . DIRECTORY_SEPARATOR . $file['relative_path']),
            ];
        }

        // Normalize associative maps to ordered lists
        $unitList = [];
        foreach ($units as $unit) {
            $sessionList = [];
            foreach ($unit['sessions'] as $session) {
                $sessionList[] = $session;
            }
            $unit['sessions'] = $sessionList;
            $unitList[] = $unit;
        }

        usort($unitList, function ($a, $b) {
            return $this->arabicOrderKey($a['title']) <=> $this->arabicOrderKey($b['title']);
        });

        foreach ($unitList as &$unit) {
            usort($unit['sessions'], function ($a, $b) {
                return $this->arabicOrderKey($a['title']) <=> $this->arabicOrderKey($b['title']);
            });
        }
        unset($unit);

        return [
            'title' => $course['title'],
            'synced_at' => now()->toIso8601String(),
            'file_count' => count($files),
            'units' => $unitList,
        ];
    }

    private function arabicOrderKey(string $title): string
    {
        $map = [
            'الأولى' => '01',
            'الاولى' => '01',
            'الثانية' => '02',
            'الثالثة' => '03',
            'الرابعة' => '04',
            'الخامسة' => '05',
            'السادسة' => '06',
            'السابعة' => '07',
            'الثامنة' => '08',
            'التاسعة' => '09',
            'العاشرة' => '10',
        ];

        foreach ($map as $needle => $rank) {
            if (mb_strpos($title, $needle) !== false) {
                return $rank . $title;
            }
        }

        return '99' . $title;
    }

    private function initializeGoogleClient(): void
    {
        $this->googleClient = new GoogleClient();
        $this->googleClient->setApplicationName('ETraining Uploaded Courses');
        $this->googleClient->setScopes([GoogleDriveService::DRIVE_READONLY]);

        $credentialsPath = storage_path('app/google-drive-credentials.json');

        if (file_exists($credentialsPath)) {
            $this->googleClient->setAuthConfig($credentialsPath);
            $this->line('Using service account credentials from file');
        } else {
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
            $this->line('Using service account credentials from environment');
        }
    }

    private function formatBytes(int $bytes, int $precision = 1): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = (int) floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }
}
