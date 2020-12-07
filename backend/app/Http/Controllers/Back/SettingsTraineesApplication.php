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
        return RequiredTraineesFiles::get();
    }
}
