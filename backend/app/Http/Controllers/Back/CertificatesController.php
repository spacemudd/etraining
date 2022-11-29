<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CertificatesController extends Controller
{
    public function import()
    {
        return Inertia::render('Back/Certificates/Import');
    }
}
