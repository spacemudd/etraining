<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    /**
     * @param  mixed  $media_id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse|StreamedResponse|\Illuminate\Http\Response
     */
    public function download(Request $request, $media_id)
    {
        $media = Media::findOrFail($media_id);

        // Check if user has limited view permission (identity only)
        if (auth()->user()->can('view-trainee-identity-only')) {
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

        // Same-origin stream for in-app PWA preview (avoids leaving the app via S3 redirect).
        if ($request->boolean('stream')) {
            return $this->streamMedia($media, $filename, $contentType);
        }

        if ($media->disk === 's3') {
            $file_url = $media->getTemporaryUrl(now()->addMinutes(5), '', [
                'ResponseContentType' => $contentType,
                'ResponseContentDisposition' => 'inline; filename="'.$filename.'"',
            ]);

            return redirect()->to($file_url);
        }

        return response()->file($media->getPath(), [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.($media->file_name ?: $filename).'"',
        ]);
    }

    /**
     * @return StreamedResponse|\Illuminate\Http\Response
     */
    private function streamMedia(Media $media, string $filename, string $contentType)
    {
        $headers = [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control' => 'private, max-age=60',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($media->disk !== 's3') {
            return response()->file($media->getPath(), $headers);
        }

        $path = $media->getPathRelativeToRoot();
        $disk = Storage::disk($media->disk);

        if (! $disk->exists($path)) {
            abort(404);
        }

        $stream = $disk->readStream($path);
        if ($stream === false) {
            abort(404);
        }

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $headers);
    }
}
