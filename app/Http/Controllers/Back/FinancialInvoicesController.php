<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\FinancialInvoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialInvoicesController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Finance/Invoices/Index', [
            'invoices' => FinancialInvoice::paginate(20),
        ]);
    }
}
