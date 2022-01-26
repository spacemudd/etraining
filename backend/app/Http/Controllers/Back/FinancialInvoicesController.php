<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Invoice;
use Inertia\Inertia;

class FinancialInvoicesController extends Controller
{
    public function index()
    {
        $this->authorize('issue-monthly-invoices');

        return Inertia::render('Back/Finance/Invoices/Index', [
            'invoices' => Invoice::latest()->paginate(20),
        ]);
    }

    public function show(string $invoice_id)
    {
        $this->authorize('issue-monthly-invoices');

        $invoice = Invoice::query()
            ->with([
                'items',
                'company',
                'trainee',
            ])
            ->findOrFail($invoice_id);

        return Inertia::render('Back/Finance/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }
}
