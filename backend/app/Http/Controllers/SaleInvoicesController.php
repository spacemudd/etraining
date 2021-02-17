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
        Inertia::setRootView('payment-layout');
        return Inertia::render('Back/SaleInvoices/Show', [
            'saleInvoice' => SaleInvoice::withoutGlobalScope(TeamScope::class)
                ->with('billable')
                ->findOrFail($sale_invoice),
        ]);
    }

    function payViaBankTransfer($sale_invoice)
    {
        Inertia::setRootView('payment-layout');
        return Inertia::render('Back/SaleInvoices/PayViaBankTransfer', [
            'saleInvoice' => SaleInvoice::withoutGlobalScope(TeamScope::class)
                ->with('billable')
                ->findOrFail($sale_invoice),
        ]);
    }
}
