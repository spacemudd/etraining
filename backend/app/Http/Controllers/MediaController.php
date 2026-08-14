<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     *
     * @param $media_id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($media_id)
    {
        $media = Media::findOrFail($media_id);

        // Check if user has limited view permission (identity only)
        if (auth()->user()->can('view-trainee-identity-only')) {
            // Only allow downloading identity files
            if ($media->collection_name !== 'identity') {
                abort(403, 'You are only authorized to view identity files.');
            }
        }

        if ($media->disk === 's3') {
            $extension = pathinfo((string) $media->file_name, PATHINFO_EXTENSION);
            if ($extension === '' && filled($media->mime_type)) {
                $extension = Str::afterLast((string) $media->mime_type, '/');
            }

            $filename = trim(Str::slug((string) $media->name) . ($extension !== '' ? '.' . $extension : ''), '.');

            $file_url = $media->getTemporaryUrl(now()->addMinutes(5), '', [
                'ResponseContentType' => $media->mime_type ?: 'application/octet-stream',
                'ResponseContentDisposition' => 'inline; filename="' . $filename . '"',
            ]);
        } else {
            return response()->file($media->getPath(), [
                'Content-Type' => $media->mime_type ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . ($media->file_name ?: $media->name) . '"',
            ]);
        }

        return redirect()->to($file_url);
    }
}
