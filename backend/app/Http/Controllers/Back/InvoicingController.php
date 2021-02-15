<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoicingController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Finance/Invoicing/Index');
    }
}
