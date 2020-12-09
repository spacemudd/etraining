<?php

namespace App\Http\Controllers;

use App\Models\Back\RequiredTraineesFiles;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TraineesApplicationController extends Controller
{
    public function index()
    {
        $required_files = RequiredTraineesFiles::get();

        return Inertia::render('Trainees/Application', [
            'required_files' => $required_files,
        ]);
    }
}
