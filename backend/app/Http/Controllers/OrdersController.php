<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class OrdersController extends Controller
{
    public function index () {
        {
            return Inertia::render('Orders/Index');
        }
    }
}
