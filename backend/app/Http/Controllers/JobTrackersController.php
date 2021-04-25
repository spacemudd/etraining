<?php

namespace App\Http\Controllers;

use App\Models\Back\ExportTraineesToExcelJobTracker;
use App\Models\JobTracker;
use Illuminate\Http\Request;

class JobTrackersController extends Controller
{
    /**
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        return JobTracker::findOrFail($id);
    }

    /**
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        $tracker = JobTracker::findOrFail($id);
        $file = $tracker->getFirstMedia('excel');

        if ($file->disk === 's3') {
            return redirect()->to($file->getTemporaryUrl(now()->addMinutes(5), '', [
                //'ResponseContentType' => 'application/octet-stream',
            ]));
        } else {
            return response()->download($file->getPath());
        }
    }
}
