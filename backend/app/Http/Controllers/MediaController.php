<?php

namespace App\Http\Controllers;

use App\Models\Media;

class MediaController extends Controller
{
    /**
     *
     * @param $media_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function download($media_id): \Illuminate\Http\RedirectResponse
    {
        $media = Media::findOrFail($media_id);

        $file_url = $media->getTemporaryUrl(now()->addMinutes(5), '', [
            //'ResponseContentType' => 'application/octet-stream', // this forces the item to be downloaded.
        ]);

        return redirect()->to($file_url);
    }
}
