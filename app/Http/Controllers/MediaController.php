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
        $file_url = Media::findOrFail($media_id)->getTemporaryUrl(now()->addMinutes(5), '', [
            'ResponseContentType' => 'application/octet-stream',
        ]);

        return redirect()->to($file_url);
    }
}
