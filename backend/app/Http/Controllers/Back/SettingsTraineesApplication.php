<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\RequiredTraineesFiles;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsTraineesApplication extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Settings/TraineesApplication/Index');
    }

    public function requiredFiles()
    {
        return RequiredTraineesFiles::orderBy(
            'name_ar',
        )->get();
    }

    /**
     * Store new required file.
     *
     * @param \Illuminate\Http\Request $request
     * @return RequiredTraineesFiles
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'default' => 'nullable|boolean',
        ]);

        RequiredTraineesFiles::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'required' => true,
        ]);

        return redirect()->route('back.settings.trainees-application');
    }

    /**
     * Deleted a trainee application requirement.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $requiredFile = RequiredTraineesFiles::find($id);
        
        if (!$requiredFile) {
            return response()->json([
                'success' => false,
                'message' => 'Required file not found'
            ], 404);
        }
        
        $requiredFile->delete();
        return response()->json([
            'success' => true,
        ]);
    }
}
