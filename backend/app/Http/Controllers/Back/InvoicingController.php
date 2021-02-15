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

    /**
     *
     */
    public function create()
    {
        return Inertia::render('Back/Finance/Invoicing/Create');
    }

    /**
     * Store a new monthly invoicing batch.
     * This is a collection of invoices for the current month.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {


        dd(1);
    }
}
