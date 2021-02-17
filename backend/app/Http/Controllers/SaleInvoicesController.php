<?php

namespace App\Http\Controllers;

use App\Models\Back\SaleInvoice;
use App\Scope\TeamScope;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleInvoicesController extends Controller
{
    public function show($sale_invoice)
    {
        return Inertia::render('Back/SaleInvoices/Show', [
            'saleInvoice' => SaleInvoice::withoutGlobalScope(TeamScope::class)
                ->findOrFail($sale_invoice),
        ]);
    }
}
