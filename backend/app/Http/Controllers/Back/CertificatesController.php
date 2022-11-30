<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Imports\CertificatesImport;
use App\Models\Back\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificatesController extends Controller
{
    public function import()
    {
        return Inertia::render('Back/Certificates/Import');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required',
        ]);

        $path = request()->file('excel_file')->store('tmp');
        $filepath = storage_path('app').'/'.$path;

        $import = new CertificatesImport();
        $import->course_id = Course::first()->id;
        $import->status = 1;
        $import->processed_count = 0;
        $import->total_count = 0;
        $import->started_at = null;
        $import->completed_at = null;


        Excel::import(new CertificatesImport(), $filepath);

        return response()->json([
            'id' => ,
        ]);
    }
}
