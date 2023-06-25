<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdersResignationsController extends Controller
{
    public function index()
    {
        return Inertia::render('Orders/Resignations/Index');
    }

    public function create()
    {
        return Inertia::render('Orders/Resignations/Create');
    }
}
