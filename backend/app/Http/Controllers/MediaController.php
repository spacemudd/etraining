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

        if ($media->disk === 's3') {
            $file_url = $media->getTemporaryUrl(now()->addMinutes(5), '', [
                //'ResponseContentType' => 'application/octet-stream', // this forces the item to be downloaded.
                'ResponseContentDisposition' => 'inline; filename ="' . $media->name .'.'.Str::beforeLast($media->mime_type, '/').'"',
            ]);
        } else {
            return response()->file($media->getPath());
        }

        return redirect()->to($file_url);
    }
}
