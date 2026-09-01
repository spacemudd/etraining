<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Scope\TeamScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MediaController extends Controller
{
    /**
     * @param  mixed  $media_id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Request $request, $media_id)
    {
        $media = Media::withoutGlobalScope(TeamScope::class)->findOrFail($media_id);

        // Check if user has limited view permission (identity only)
        if (auth()->user() && auth()->user()->can('view-trainee-identity-only')) {
            // Only allow downloading identity files
            if ($media->collection_name !== 'identity') {
                abort(403, 'You are only authorized to view identity files.');
            }
        }

        $extension = pathinfo((string) $media->file_name, PATHINFO_EXTENSION);
        if ($extension === '' && filled($media->mime_type)) {
            $extension = Str::afterLast((string) $media->mime_type, '/');
        }

        $filename = trim(Str::slug((string) $media->name).($extension !== '' ? '.'.$extension : ''), '.');
        if ($filename === '') {
            $filename = $media->file_name ?: 'file';
        }

        $contentType = $media->mime_type ?: 'application/octet-stream';

        if ($media->disk === 's3' || $request->boolean('stream')) {
            return $this->streamMedia($media, $filename, $contentType);
        }

        return response()->file($media->getPath(), [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.($media->file_name ?: $filename).'"',
        ]);
    }

    /**
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function streamMedia(Media $media, string $filename, string $contentType)
    {
        $headers = [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control' => 'private, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($media->size) {
            $headers['Content-Length'] = (string) $media->size;
        }

        if ($media->disk !== 's3') {
            return response()->file($media->getPath(), $headers);
        }

        $path = method_exists($media, 'getPathRelativeToRoot')
            ? $media->getPathRelativeToRoot()
            : $media->getPath();

        try {
            $disk = Storage::disk($media->disk);
            if ($path && $disk->exists($path)) {
                $stream = $disk->readStream($path);
                if (is_resource($stream)) {
                    return $this->streamResource($stream, $headers);
                }
            }
        } catch (Throwable $exception) {
            Log::warning('Media stream via disk failed; falling back to temporary URL', [
                'media_id' => $media->id,
                'error' => $exception->getMessage(),
            ]);
        }

        try {
            $temporaryUrl = $media->getTemporaryUrl(now()->addMinutes(5), '', [
                'ResponseContentType' => $contentType,
                'ResponseContentDisposition' => 'inline; filename="'.$filename.'"',
            ]);

            $remote = Http::timeout(60)->get($temporaryUrl);
            if ($remote->successful()) {
                return response($remote->body(), 200, $headers);
            }

            Log::warning('Media stream temporary URL fetch failed', [
                'media_id' => $media->id,
                'status' => $remote->status(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Media stream temporary URL exception', [
                'media_id' => $media->id,
                'error' => $exception->getMessage(),
            ]);
        }

        abort(404);
    }

    /**
     * @param  resource  $stream
     */
    private function streamResource($stream, array $headers): StreamedResponse
    {
        return response()->stream(static function () use ($stream): void {
            try {
                fpassthru($stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 200, $headers);
    }
}
