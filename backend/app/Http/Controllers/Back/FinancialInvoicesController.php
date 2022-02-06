<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\Invoice;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PDF;
use Spatie\QueryBuilder\QueryBuilder;

class FinancialInvoicesController extends Controller
{
    public function index()
    {
        $this->authorize('issue-monthly-invoices');

        $invoices = QueryBuilder::for(Invoice::class)
            ->with(['trainee' => function($q) {
                $q->with('company');
            }])
            ->with('company')
            ->defaultSort('created_at')
            ->allowedSorts(['created_at', 'number', 'status', 'payment_method', 'grand_total', 'is_verified', 'created_at'])
            ->allowedFilters(['created_at', 'trainee.name', 'number', 'company.name_ar', 'status'])
            ->allowedFields(['trainee.id', 'trainee.name', 'company.id', 'company.name_ar'])
            ->allowedIncludes(['company', 'trainee'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Finance/Invoices/Index', [
            'invoices' => $invoices,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addSearchRows([
                'number' => __('words.invoice-number'),
                'company.name_ar' => __('words.company'),
                'trainee.name' => __('words.trainee'),
                'created_at' => __('words.date'),
            ]);

            $table->addFilter('status', __('words.status'), [
                Invoice::STATUS_UNPAID => __('words.unpaid'),
                Invoice::STATUS_AUDIT_REQUIRED => __('words.audit-required'),
                Invoice::STATUS_PAYMENT_RECEIPT_REJECTED => __('words.reject-payment-receipt'),
                Invoice::STATUS_PAID =>  __('words.paid'),
            ]);
        });
    }

    public function show(string $invoice_id)
    {
        $this->authorize('issue-monthly-invoices');

        $invoice = Invoice::query()
            ->with([
                'items',
                'company',
                'trainee',
                'trainee_bank_payment_receipt',
                'verified_by',
            ])
            ->findOrFail($invoice_id);

        return Inertia::render('Back/Finance/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function expectedAmountPerInvoice(Request $request)
    {
        $request->validate([
            'from_date' => 'required',
            'to_date' => 'required',
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::findOrFail($request->company_id);

        // TODO: account for cases where its spanning onto the next month.
        // (e.g. the daily charge for a month of 31 days is different from one with 28 days)
        $from_date = Carbon::parse($request->from_date)->startOfDay();
        $to_date = Carbon::parse($request->to_date)->endOfDay();

        $days_to_charge = $from_date->diffInDays($to_date) + 1;

        $cost = Money::of($company->monthly_subscription_per_trainee, 'SAR', new CustomContext(5), RoundingMode::HALF_UP)
            ->dividedBy($from_date->daysInMonth, RoundingMode::HALF_UP)
            ->multipliedBy($days_to_charge)
            ->to(new CustomContext(2), RoundingMode::HALF_UP)
            ->getAmount()
            ->toFloat();

        return response()->json([
            'cost' => $cost,
        ]);
    }

    public function pdf($id)
    {
        $invoice = Invoice::findOrFail($id);
        $pdf = PDF::setOption('footer-html', resource_path('views/pdf/invoices/client-invoice-footer.html'))
            ->setOption('margin-bottom', 30)
        ->loadView('pdf.invoices.show', [
            'title' => 'Invoice',
            'invoice' => $invoice,
        ]);
        return $pdf->inline();
    }

    public function rejectPaymentReceipt($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->status = Invoice::STATUS_PAYMENT_RECEIPT_REJECTED;
        $invoice->rejection_reason_payment_receipt = $request->reason;
        $invoice->save();

        return redirect()->route('back.finance.invoices.show', $invoice->id);
    }

    public function approvePaymentReceipt($id)
    {
        return Inertia::render('Back/Finance/Invoices/ApprovePaymentReceipt', [
            'invoice' => Invoice::findOrFail($id),
        ]);
    }

    public function storePaymentReceiptProof($id, Request $request)
    {
        $request->validate([
            'files.*' => 'required|file',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->status = Invoice::STATUS_PAID;
        $invoice->rejection_reason_payment_receipt = null;
        $invoice->verified_by_id = auth()->user()->id;
        $invoice->save();


        foreach ($request->file('files', []) as $key => $file) {
            $invoice->trainee_bank_payment_receipt->uploadToFolder($file, 'receipt-approvals');
        }

        return redirect()->route('back.finance.invoices.show', $id);
    }
}
