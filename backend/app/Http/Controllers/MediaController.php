<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     *
     * @param $media_id
     * @return \Illuminate\Http\RedirectResponse
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
            $file_url = $media->getTemporaryUrl(now()->addMinutes(5), '', [
                //'ResponseContentType' => 'application/octet-stream', // this forces the item to be downloaded.
                'ResponseContentDisposition' => 'inline; filename ="' . Str::slug($media->name) .'.'.Str::beforeLast($media->mime_type, '/').'"',
            ]);
        } else {
            return response()->file($media->getPath());
        }

        return redirect()->to($file_url);
    }
}
