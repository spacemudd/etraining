<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Jobs\CourseAttendanceReportJob;
use App\Jobs\InvoicesSheetReportJob;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Company;
use App\Models\Back\Invoice;
use App\Models\JobTracker;
use App\Models\TraineeBankPaymentReceipt;
use App\Reports\InvoicesReportFactory;
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
                Invoice::STATUS_PAYMENT_RECEIPT_REJECTED => __('words.reject-payment-receipt'),
                Invoice::STATUS_AUDIT_REQUIRED => __('words.audit-required'),
                Invoice::STATUS_FINANCIAL_AUDIT_REQUIRED =>  __('words.finance-audit-required'),
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
                'chased_by',
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
            ->multipliedBy($days_to_charge, RoundingMode::HALF_UP)
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

        \DB::beginTransaction();

        $invoice = Invoice::findOrFail($id);
        $invoice->status = Invoice::STATUS_PAID;
        $invoice->rejection_reason_payment_receipt = null;
        $invoice->verified_by_id = auth()->user()->id;
        $invoice->verified_at = now();
        $invoice->save();

        foreach ($request->file('files', []) as $key => $file) {
            $invoice->trainee_bank_payment_receipt->uploadToFolder($file, 'receipt-approvals');
        }

        AccountingLedgerBook::create([
            'team_id' => $invoice->team_id,
            'company_id' => $invoice->company_id,
            'trainee_id' => $invoice->trainee_id,
            'invoice_id' => $invoice->id,
            'trainee_bank_payment_receipt_id' => $invoice->trainee_bank_payment_receipt->id,
            'date' => now(),
            'description' => 'اعتماد ايصال دفع للفاتورة رقم '.$invoice->number_formatted,
            'reference'  => '',
            'account_name' => $invoice->trainee->name,
            'credit' => $invoice->grand_total,
            'balance' => AccountingLedgerBook::getBalanceForTrainee($invoice->trainee->id) - $invoice->grand_total,
        ]);

        \DB::commit();

        return redirect()->route('back.finance.invoices.show', $id);
    }

    public function markAsUnpaidFromChaser($id, Request $request)
    {
        $request->validate([
            'chased_note' => 'required|string|max:255',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->status = Invoice::STATUS_UNPAID;
        $invoice->chased_note = $request->reason;
        $invoice->chased_by_id = auth()->user()->id;
        $invoice->paid_at = null;
        $invoice->save();

        return redirect()->route('back.finance.invoices.show', $invoice->id);
    }

    public function markAsPaidFromChaser($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->status = Invoice::STATUS_FINANCIAL_AUDIT_REQUIRED;
        $invoice->chased_at = now();
        $invoice->chased_by_id = auth()->user()->id;
        $invoice->save();

        return redirect()->route('back.finance.invoices.show', $id);
    }


    public function uploadReceiptForm($id)
    {
        $invoice = Invoice::findOrFail($id);
        $pending_amount = $invoice->trainee->total_amount_owed;
        return Inertia::render('Back/Finance/Invoices/UploadReceipt', [
            'pending_amount' =>  number_format($pending_amount, 2),
            'pending_amount_raw' => $pending_amount,
            'trainee' => $invoice->trainee,
            'invoice' => $invoice,
        ]);
    }

    public function uploadReceipt($id, Request $request)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'sender_name' => 'required|string|max:255|min:3',
            'bank_name_to' => 'required|string|max:255|min:5',
            'bank_name_from' => 'required|string|max:255|min:5',
            'files.*' => 'file',
        ]);

        \DB::beginTransaction();
        $receipt = new TraineeBankPaymentReceipt();
        $receipt->trainee_id = $invoice->trainee_id;
        $receipt->amount = $request->amount;
        $receipt->sender_name = $request->sender_name;
        $receipt->bank_from = $request->bank_name_from;
        $receipt->bank_to = $request->bank_name_to;
        $receipt->uploaded_by_id = auth()->user()->id;
        $receipt->save();

        if ($request->has('files')) {
            foreach ($request->file('files', []) as $key => $file) {
                $receipt->uploadToFolder($file, 'receipts');
            }
        }

        $invoice->payment_method =  Invoice::PAYMENT_METHOD_BANK_RECEIPT;
        $invoice->trainee_bank_payment_receipt_id = $receipt->id;
        $invoice->paid_at = now();
        $invoice->status = Invoice::STATUS_FINANCIAL_AUDIT_REQUIRED;
        $invoice->rejection_reason_payment_receipt = null;
        $invoice->verified_by_id = null;
        $invoice->chased_at = now();
        $invoice->chased_by_id = auth()->user()->id;
        $invoice->save();

        \DB::commit();

        return redirect()->route('back.finance.invoices.show', $id);
    }

    public function excel()
    {
        $this->authorize('view-backoffice-reports');
        return Inertia::render('Back/Finance/Invoices/Excel', [
            'companies' => Company::orderBy('name_ar')->get(),
        ]);
    }

    public function generateExcel(Request $request)
    {
        $request->validate([
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $tracker = new JobTracker();
        $tracker->user_id = auth()->user()->id;
        $tracker->metadata = $request->except('_token');
        $tracker->reportable_id = null;
        $tracker->reportable_type = InvoicesReportFactory::class;
        $tracker->queued_at = now();
        $tracker->save();

        $tracker = $tracker->refresh();

        InvoicesSheetReportJob::dispatch($tracker);

        return $tracker;
    }
}
