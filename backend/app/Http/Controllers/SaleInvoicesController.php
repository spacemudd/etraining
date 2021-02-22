<?php

namespace App\Http\Controllers;

use App\Models\Back\Payment;
use App\Models\Back\SaleInvoice;
use App\Scope\TeamScope;
use Brick\Money\Money;
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

    public function uploadBankTransferReceipt($sale_invoice, Request $request)
    {
        $request->validate([
            'bank_receipt' => 'required|file',
            'amount_transferred' => 'required|numeric',
            'sender_name' => 'required|string|max:50',
            'sender_bank' => 'required|string|max:50',
        ]);

        $saleInvoice = SaleInvoice::findOrFail($sale_invoice);

        $payment = new Payment();
        $payment->team_id = $saleInvoice->team_id;
        $payment->sale_invoice_id = $saleInvoice->id;
        $payment->sender_name = $request->sender_name;
        $payment->sender_bank = $request->sender_bank;
        $payment->amount = Money::of($request->amount_transferred, 'SAR')->getMinorAmount()->toInt();
        $payment->method = 'transfer';
        $payment->status = Payment::STATUS_UNDER_REVIEW;
        $payment->confirmed_at = null;
        $payment->confirmed_by = null;
        $payment->save();

        if ($file = $request->file('bank_receipt')) {
            $payment->uploadToFolder($file, 'bank_receipt');
        }

        return redirect()->route('sale-invoices.show', $saleInvoice->id);
    }
}
