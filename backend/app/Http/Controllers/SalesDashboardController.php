<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SalesDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Sales/Dashboard');
    }
}
