<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Models\Media;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Str;

class TraineesFilesController extends Controller
{
    public function index($trainee_id)
    {
       
        $trainee = Trainee::withTrashed()->with('general_files')
        ->findOrFail($trainee_id);
        return Inertia::render('Back/Trainees/Files/Index', [
            'trainee' => $trainee,
        ]);
    }

        public function store($trainee_id)
        {

            $trainee = Trainee::withTrashed()->findOrFail($trainee_id);
            $trainee->addMediaFromRequest('attached_file')
                ->usingFileName(request()->file('attached_file')->hashName())
                ->toMediaCollection('general_files');
                return redirect()->route('back.trainees.files.index', $trainee->id);
        }

    public function show($trainee_id, $file)
    {
        $media = Media::findOrFail($file);
        if ($media->disk === 's3') {
            return redirect()->to($media->getTemporaryUrl(now()->addMinutes(5), '', [
                //'ResponseContentType' => 'application/octet-stream',
            ]));
        } else {
            return response()->file($media->getPath());
        }
    }

    public function destroy($trainee_id, $file)
    {
        $media = Media::findOrFail($file);
        $media->delete();
        return redirect()->route('back.trainees.files.index', $trainee_id);
    }
}
