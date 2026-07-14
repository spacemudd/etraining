<?php

declare(strict_types=1);

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Vision;
use Google\Service\Vision\AnnotateImageRequest;
use Google\Service\Vision\BatchAnnotateImagesRequest;
use Google\Service\Vision\Feature;
use Google\Service\Vision\Image;
use Google\Service\Vision\ImageContext;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

class IdentityDocumentOcrService
{
    private const SUPPORTED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/bmp',
        'image/webp',
        'image/tiff',
        'application/pdf',
    ];

    private const BOILERPLATE_PATTERNS = [
        'kingdom',
        'saudi',
        'arabia',
        'ministry',
        'interior',
        'national',
        'identity',
        'residence',
        'permit',
        'iqama',
        'passport',
        'date of birth',
        'place of birth',
        'nationality',
        'expiry',
        'expiration',
        'issue',
        'sex',
        'gender',
        'version',
        'card',
        'document',
        'number',
        'serial',
    ];

    /**
     * Read an uploaded identity document via Google Cloud Vision and return the English name.
     */
    public function extractEnglishNameFromUpload(UploadedFile $file): ?string
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $path = $file->getRealPath();
        if (! $path || ! is_readable($path)) {
            Log::warning('Identity OCR skipped: uploaded file is not readable');

            return null;
        }

        $mimeType = (string) ($file->getMimeType() ?: $file->getClientMimeType());

        // Some browsers/servers send PDFs as octet-stream.
        if (
            ! $this->supportsMimeType(strtolower($mimeType))
            && strtolower((string) $file->getClientOriginalExtension()) === 'pdf'
        ) {
            $mimeType = 'application/pdf';
        }

        return $this->extractEnglishNameFromContents(
            (string) file_get_contents($path),
            $mimeType
        );
    }

    /**
     * OCR raw image bytes and extract an English person name.
     */
    public function extractEnglishNameFromContents(string $contents, string $mimeType = 'image/jpeg'): ?string
    {
        if (! $this->isEnabled()) {
            return null;
        }

        if ($contents === '') {
            return null;
        }

        $mimeType = strtolower($mimeType);

        if (! $this->supportsMimeType($mimeType)) {
            Log::info('Identity OCR skipped: unsupported mime type', [
                'mime_type' => $mimeType,
            ]);

            return null;
        }

        try {
            if ($mimeType === 'application/pdf') {
                $contents = $this->convertPdfFirstPageToJpeg($contents);
                if ($contents === null) {
                    return null;
                }
            }

            $text = $this->detectDocumentText($contents);
        } catch (Throwable $e) {
            Log::error('Identity OCR Vision API failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        if ($text === null || trim($text) === '') {
            return null;
        }

        return $this->parseEnglishNameFromOcrText($text);
    }

    public function isEnabled(): bool
    {
        return (bool) config('services.google.vision_identity_ocr', false);
    }

    public function supportsMimeType(string $mimeType): bool
    {
        return in_array(strtolower($mimeType), self::SUPPORTED_MIME_TYPES, true);
    }

    /**
     * Parse English name lines from Vision OCR text (Saudi ID / Iqama oriented).
     */
    public function parseEnglishNameFromOcrText(string $text): ?string
    {
        $lines = preg_split('/\R/u', $text) ?: [];
        $normalized = array_values(array_filter(array_map(
            static fn ($line) => trim(preg_replace('/\s+/u', ' ', (string) $line) ?? ''),
            $lines
        )));

        for ($i = 0, $count = count($normalized); $i < $count; $i++) {
            $line = $normalized[$i];

            if (preg_match('/^(?:name|full\s*name)\s*:?\s*(.+)$/iu', $line, $matches)) {
                $candidate = trim($matches[1]);
                if ($this->looksLikeEnglishPersonName($candidate)) {
                    return $this->normalizeEnglishName($candidate);
                }
            }

            if (preg_match('/^(?:name|full\s*name)\s*:?\s*$/iu', $line)
                || preg_match('/^(?:الاسم|الاسم الكامل)\s*:?\s*$/u', $line)
                || preg_match('/الاسم.*\bname\b/iu', $line)
                || preg_match('/\bname\b.*الاسم/iu', $line)
            ) {
                for ($j = $i + 1; $j < min($i + 6, $count); $j++) {
                    if ($this->looksLikeEnglishPersonName($normalized[$j])) {
                        return $this->normalizeEnglishName($normalized[$j]);
                    }
                }
            }
        }

        $candidates = [];
        foreach ($normalized as $line) {
            if ($this->looksLikeEnglishPersonName($line) && ! $this->isBoilerplate($line)) {
                $candidates[] = $line;
            }
        }

        if ($candidates === []) {
            return null;
        }

        usort($candidates, static function (string $a, string $b): int {
            $wordsA = str_word_count($a);
            $wordsB = str_word_count($b);
            if ($wordsA !== $wordsB) {
                return $wordsB <=> $wordsA;
            }

            return strlen($b) <=> strlen($a);
        });

        return $this->normalizeEnglishName($candidates[0]);
    }

    private function detectDocumentText(string $contents): ?string
    {
        $client = $this->makeGoogleClient();
        $vision = new Vision($client);

        $image = new Image();
        $image->setContent(base64_encode($contents));

        $feature = new Feature();
        $feature->setType('DOCUMENT_TEXT_DETECTION');

        $imageContext = new ImageContext();
        $imageContext->setLanguageHints(['en', 'ar']);

        $request = new AnnotateImageRequest();
        $request->setImage($image);
        $request->setFeatures([$feature]);
        $request->setImageContext($imageContext);

        $batchRequest = new BatchAnnotateImagesRequest();
        $batchRequest->setRequests([$request]);

        $response = $vision->images->annotate($batchRequest);
        $annotations = $response->getResponses();

        if (empty($annotations)) {
            return null;
        }

        $annotation = $annotations[0];

        if ($annotation->getError()) {
            Log::error('Identity OCR Vision annotation error', [
                'error' => $annotation->getError()->getMessage(),
            ]);

            return null;
        }

        $fullText = $annotation->getFullTextAnnotation();

        return $fullText ? $fullText->getText() : null;
    }

    /**
     * Convert the first page of a PDF into a JPEG suitable for Vision images.annotate.
     */
    private function convertPdfFirstPageToJpeg(string $pdfContents): ?string
    {
        if (! extension_loaded('imagick') || ! class_exists(\Imagick::class)) {
            Log::warning('Identity OCR PDF skipped: Imagick extension is not available');

            return null;
        }

        try {
            $imagick = new \Imagick();
            $imagick->setResolution(300, 300);
            $imagick->readImageBlob($pdfContents);
            $imagick->setIteratorIndex(0);

            $page = $imagick->getImage();
            $page->setImageBackgroundColor('white');
            if (defined('Imagick::ALPHACHANNEL_REMOVE')) {
                $page->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
            }
            if (defined('Imagick::LAYERMETHOD_FLATTEN')) {
                $page = $page->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
            }
            $page->setImageFormat('jpeg');
            $page->setImageCompressionQuality(90);

            $jpeg = $page->getImageBlob();

            $page->clear();
            $page->destroy();
            $imagick->clear();
            $imagick->destroy();

            return $jpeg !== '' ? $jpeg : null;
        } catch (Throwable $e) {
            Log::error('Identity OCR failed to convert PDF to image', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function makeGoogleClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setApplicationName('ETraining Identity OCR');
        $client->setScopes(['https://www.googleapis.com/auth/cloud-vision']);

        $vision = config('services.google.vision', []);

        if (empty($vision['client_email']) || empty($vision['private_key'])) {
            throw new \RuntimeException(
                'Google Vision credentials are missing. Set GOOGLE_VISION_CLIENT_EMAIL and GOOGLE_VISION_PRIVATE_KEY in .env.'
            );
        }

        $client->setAuthConfig([
            'type' => 'service_account',
            'project_id' => $vision['project_id'],
            'private_key_id' => $vision['private_key_id'],
            'private_key' => $vision['private_key'],
            'client_email' => $vision['client_email'],
            'client_id' => $vision['client_id'],
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url' => $vision['client_x509_cert_url'],
        ]);

        return $client;
    }

    private function looksLikeEnglishPersonName(string $line): bool
    {
        if ($line === '' || mb_strlen($line) < 3 || mb_strlen($line) > 80) {
            return false;
        }

        if ($this->isBoilerplate($line)) {
            return false;
        }

        // Must be predominantly Latin letters (Saudi IDs print English names in Latin script).
        // Allow commas for "SURNAME, GIVEN NAMES" (common on Saudi national ID cards).
        if (! preg_match('/^[A-Za-z][A-Za-z\s\'\-\.,]+$/u', $line)) {
            return false;
        }

        $letterCount = preg_match_all('/[A-Za-z]/', $line);
        if ($letterCount < 3) {
            return false;
        }

        // Split on spaces/commas so "ALDOUYSH, MONA ABDULAZIZ A" counts as multiple name parts.
        $words = preg_split('/[\s,]+/u', $line) ?: [];
        $words = array_values(array_filter($words));

        if (count($words) < 2) {
            return false;
        }

        // Allow single-letter parts (father-name initial, e.g. trailing "A").
        foreach ($words as $word) {
            if (! preg_match('/^[A-Za-z\'\-\.]+$/u', $word)) {
                return false;
            }
        }

        return true;
    }

    private function isBoilerplate(string $line): bool
    {
        $lower = strtolower($line);

        foreach (self::BOILERPLATE_PATTERNS as $pattern) {
            if (str_contains($lower, $pattern)) {
                return true;
            }
        }

        return (bool) preg_match('/\d{4,}/', $line);
    }

    private function normalizeEnglishName(string $name): string
    {
        $name = trim(preg_replace('/\s+/u', ' ', $name) ?? '');
        // Keep spacing tidy around commas: "ALDOUYSH,MONA" → "ALDOUYSH, MONA"
        $name = preg_replace('/\s*,\s*/u', ', ', $name) ?? $name;
        $name = trim($name);

        // Keep Saudi ID "SURNAME, GIVEN..." uppercase style as printed on the card.
        if (str_contains($name, ',')) {
            return mb_strtoupper($name, 'UTF-8');
        }

        // Title-case names that come back fully uppercase from the ID card.
        if ($name === strtoupper($name) && preg_match('/[A-Z]/', $name)) {
            $name = mb_convert_case(mb_strtolower($name), MB_CASE_TITLE, 'UTF-8');
        }

        return $name;
    }
}
