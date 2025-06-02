<?php

namespace App\Http\Controllers\Back;

use App\Mail\EditInvoiceMail;
use App\Http\Controllers\Controller;
use App\Jobs\CourseAttendanceReportJob;
use App\Jobs\InvoicesSheetReportJob;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Company;
use App\Models\Back\Invoice;
use App\Models\Back\InvoiceItem;
use App\Models\JobTracker;
use App\Models\TraineeBankPaymentReceipt;
use App\Models\User;
use App\Reports\InvoicesReportFactory;
use App\Services\NoonService;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Mail;
use PDF;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class  FinancialInvoicesController extends Controller
{
    public function index()
    {
        $this->authorize('issue-monthly-invoices');

        $invoices = QueryBuilder::for(Invoice::class)
            ->with(['trainee' => function($q) {
                $q->with('company');
            }])
            ->with('company')
            ->with('trainee_bank_payment_receipt')
            ->defaultSort('-created_at')
            ->allowedSorts(['from_date','created_at', 'number', 'status', 'payment_method', 'grand_total', 'is_verified', 'created_at'])
            ->allowedFilters(['payment_method','trainee_bank_payment_receipt.bank_to','trainee_bank_payment_receipt.bank_from','from_date','created_at', 'trainee.name', 'number', 'company.name_ar', AllowedFilter::exact('status'), 'trainee_bank_payment_receipt.sender_name', 'trainee_bank_payment_receipt.created_at'])
            ->allowedFields(['payment_method','trainee_bank_payment_receipt.bank_to','trainee_bank_payment_receipt.bank_from','trainee.id', 'trainee.name', 'company.id', 'company.name_ar', 'trainee_bank_payment_receipt.sender_name', 'trainee_bank_payment_receipt.created_at'])
            ->allowedIncludes(['company', 'trainee', 'trainee_bank_payment_receipt'])
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
                'created_at' => __('words.date'),
                'trainee_bank_payment_receipt.created_at' => __('words.receipt-date'),
                'trainee_bank_payment_receipt.sender_name' => __('words.sender-name'),
                'from_date' => __('words.date-period'),
                'trainee_bank_payment_receipt.bank_from' => __('words.sender-bank-name'),
                'trainee_bank_payment_receipt.bank_to' => __('words.receiver-bank-name'),
            ]);

            $table->addFilter('status', __('words.status'), [
                Invoice::STATUS_PENDING_CHECK => __('words.under-review'),
                Invoice::STATUS_ARCHIVED => __('words.archived'),
                Invoice::STATUS_UNPAID => __('words.unpaid'),
                Invoice::STATUS_PAYMENT_RECEIPT_REJECTED => __('words.reject-payment-receipt'),
                Invoice::STATUS_AUDIT_REQUIRED => __('words.audit-required'),
                Invoice::STATUS_FINANCIAL_AUDIT_REQUIRED =>  __('words.finance-audit-required'),
                Invoice::STATUS_PAID =>  __('words.paid'),
            ]);

            $table->addFilter('payment_method', __('words.payment-method'), [
                Invoice::PAYMENT_METHOD_BANK_RECEIPT => __('words.bank-transfer'),
                Invoice::PAYMENT_METHOD_CREDIT_CARD => __('words.credit-card-method'),
            ]);
        });
    }

    public function ShortTable()
    {
//        $this->authorize('issue-monthly-invoices');

        $invoices = QueryBuilder::for(Invoice::class)
            ->with(['trainee' => function($q) {
                $q->with('company');
            }])
            ->with('company')
            ->with('trainee_bank_payment_receipt')
            ->defaultSort('-created_at')
            ->allowedSorts(['from_date','created_at', 'number', 'status', 'payment_method', 'grand_total', 'is_verified', 'created_at'])
            ->allowedFilters(['payment_method','trainee_bank_payment_receipt.bank_to','trainee_bank_payment_receipt.bank_from','from_date','created_at', 'trainee.name', 'number', 'company.name_ar', 'status', 'trainee_bank_payment_receipt.sender_name', 'trainee_bank_payment_receipt.created_at'])
            ->allowedFields(['payment_method','trainee_bank_payment_receipt.bank_to','trainee_bank_payment_receipt.bank_from','trainee.id', 'trainee.name', 'company.id', 'company.name_ar', 'trainee_bank_payment_receipt.sender_name', 'trainee_bank_payment_receipt.created_at'])
            ->allowedIncludes(['company', 'trainee', 'trainee_bank_payment_receipt'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Finance/Invoices/ShortTable', [
            'invoices' => $invoices,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addSearchRows([
                'number' => __('words.invoice-number'),
                'company.name_ar' => __('words.company'),
                'trainee.name' => __('words.trainee'),
                'created_at' => __('words.date'),
                'created_at' => __('words.date'),
                'trainee_bank_payment_receipt.created_at' => __('words.receipt-date'),
                'trainee_bank_payment_receipt.sender_name' => __('words.sender-name'),
                'from_date' => __('words.date-period'),
                'trainee_bank_payment_receipt.bank_from' => __('words.sender-bank-name'),
                'trainee_bank_payment_receipt.bank_to' => __('words.receiver-bank-name'),
            ]);

            $table->addFilter('status', __('words.status'), [
                Invoice::STATUS_PENDING_CHECK => __('words.under-review'),
                Invoice::STATUS_ARCHIVED => __('words.archived'),
                Invoice::STATUS_UNPAID => __('words.unpaid'),
                Invoice::STATUS_PAYMENT_RECEIPT_REJECTED => __('words.reject-payment-receipt'),
                Invoice::STATUS_AUDIT_REQUIRED => __('words.audit-required'),
                Invoice::STATUS_FINANCIAL_AUDIT_REQUIRED =>  __('words.finance-audit-required'),
                Invoice::STATUS_PAID =>  __('words.paid'),
            ]);

            $table->addFilter('payment_method', __('words.payment-method'), [
                Invoice::PAYMENT_METHOD_BANK_RECEIPT => __('words.bank-transfer'),
                Invoice::PAYMENT_METHOD_CREDIT_CARD => __('words.credit-card-method'),
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

        $cost = Money::of($company->monthly_subscription_per_trainee ?? 0, 'SAR', new CustomContext(5), RoundingMode::HALF_UP)
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

//        $pdf = PDF::setOption('footer-html', resource_path('views/pdf/invoices/client-invoice-footer.html'))
//            ->setOption('margin-bottom', 30)
//        ->loadView('pdf.invoices.show', [
//            'title' => 'Invoice',
//            'invoice' => $invoice,
//        ]);

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
        $invoice->status = Invoice::STATUS_PAYMENT_RECEIPT_REJECTED;
        $invoice->chased_note = $request->chased_note;
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
        $pending_amount = $invoice->grand_total;

        if (auth()->user()->trainee) {
            $invoices = Invoice::where('trainee_id', auth()->user()->trainee->id)->notPaid()->get();
        } else {
            $invoices = null;
        }

        return Inertia::render('Back/Finance/Invoices/UploadReceipt', [
        'pending_amount' =>  number_format($pending_amount, 2),
        'pending_amount_raw' => $pending_amount,
        'trainee' => $invoice->trainee,
        'invoice' => $invoice,
        'invoices' =>$invoices,
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

    public function bulkApproveFinancialDepartment(Request $request)
    {
        \DB::beginTransaction();
        Invoice::whereIn('id', $request->invoices)
            ->where('status', Invoice::STATUS_FINANCIAL_AUDIT_REQUIRED)
            ->chunk(100, function($invoices) {
                foreach ($invoices as $invoice) {
                    $invoice->status = Invoice::STATUS_PAID;
                    $invoice->rejection_reason_payment_receipt = null;
                    $invoice->verified_by_id = auth()->user()->id;
                    $invoice->verified_at = now();
                    $invoice->save();

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
                }
            });
        \DB::commit();

        return response()->json([
            'success' => true,
            'invoices' => $request->invoices,
        ]);
    }
    public function update(Request $request, string $invoice_id)
    {
        $request->validate([
            'grand_total' => 'nullable|string|max:255',
            'sub_total' => 'nullable|string|max:255',
            'tax' => 'nullable|string|max:255',
            'edit_amount_reason' => 'nullable|string|max:255',
        ]);

        $invoice = Invoice::findOrFail($invoice_id);

        $company = Company::query()->findOrFail($invoice->company_id);

        DB::beginTransaction();

        $t = Invoice::where('status', Invoice::STATUS_UNPAID)
            ->where('payment_reference_id', null)
            ->find($invoice_id);

        $new = $t->replicate();
        $new->grand_total = $request->grand_total;
        $new->sub_total = $request->grand_total-($request->grand_total/1.15*0.15);
        $new->tax = (($request->grand_total-($request->grand_total/1.15*0.15))*0.15);
        $new->edit_amount_reason = $request->edit_amount_reason;
        $new->save();

        $period = [
            'start' => Carbon::parse($invoice->from_date)->format('Y-m-d'),
            'end' => Carbon::parse($invoice->to_date)->format('Y-m-d'),
        ];

        $new->items()->create([
            'name_en' => $company->name_en . ' - ' .__('words.training-costs-for-the-period-of', $period, 'en'),
            'name_ar' => $company->name_ar . ' - ' .__('words.training-costs-for-the-period-of', $period, 'ar'),
            'sub_total' => $new->sub_total,
            'tax' => $new->tax,
            'grand_total' => $new->grand_total,
        ]);

        AccountingLedgerBook::create([
            'team_id' => $new->team_id,
            'company_id' => $new->company_id,
            'trainee_id' => $new->trainee_id,
            'invoice_id' => $new->id,
            'date' => now(),
            'description' => $company->name_ar . ' - ' .__('words.training-costs-for-the-period-of', $period, 'ar'),
            'reference'  => __('words.training-costs-for-the-period-of', $period, 'ar'),
            'account_name' => $new->trainee->name,
            'debit' => $new->grand_total,
            'balance' => AccountingLedgerBook::getBalanceForTrainee($new->trainee->id) + $new->grand_total,
        ]);

        $t->delete();
        // todo: copy invoice_items too

        $users = User::permission('edit-invoice-amount')->get();
        Mail::bcc($users)
            ->queue(new EditInvoiceMail($new, $t, auth()->user()->email));

        DB::commit();

        return redirect(\route('back.finance.invoices.index'));
    }

    public function datePeriod(Request $request, string $invoice_id)
    {

        $invoice = Invoice::findOrFail($invoice_id);

        $company = Company::query()->withTrashed()->findOrFail($invoice->company_id);

        return Inertia::render('Back/Finance/Invoices/ChangeDatePeriod', [
            'company' => $company,
            'invoice' => $invoice,
            'old_from_date' => $request->input('from_date'),
            'old_to_date' => $request->input('to_date'),
            'created_at' => $request->input('created_at_date'),
            'created_by_id' => $request->input('created_by_id'),
        ])->table(function ($table) {
            $table->disableGlobalSearch();
        });
    }

    public function changeDatePeriod(Request $request, string $invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);

        $company = Company::query()->withTrashed()->findOrFail($invoice->company_id);

        DB::beginTransaction();

        $invoice->from_date = Carbon::parse($request->input('from_date'));
        $invoice->to_date = Carbon::parse($request->input('to_date'));
        $invoice->created_by_id = auth()->user()->id;
        $invoice->save();

        DB::commit();

        return redirect(\route('back.finance.invoices.index'));
    }

    public function destroy($invoice_id, Request $request)
    {
        $this->authorize('can-delete-invoice-anytime');

        $request->validate([
            'deleted_reason' => 'required|string',
        ]);

        DB::beginTransaction();

        if (request()->has('to_date')) {

            $invoices = Invoice::where('from_date', request()->input('from_date'))
                ->where('to_date', request()->input('to_date'))
                ->where('created_by_id', request()->input('created_by_id', auth()->id()))
                ->whereDate('created_at', request()->input('created_at_date', now()->toDateString()))
                ->where('company_id', request()->company_id)
                ->get();

            foreach ($invoices as $invoice) {
                if(!$invoice->paid_at){
                    AccountingLedgerBook::where('invoice_id', $invoice->id)->delete();
                    $invoice->deleted_reason = $request->deleted_reason;
                    $invoice->save();
                    $invoice->delete();
                }
            }
        } else {
            $invoice = Invoice::notPaid()->findOrFail($invoice_id);
            $invoice->deleted_reason = $request->deleted_reason;
            $invoice->save();
            AccountingLedgerBook::where('invoice_id', $invoice->id)->delete();
            $invoice->delete();
        }

        DB::commit();


        if (Str::contains(redirect()->back()->getTargetUrl(), 'companies')) {
            return redirect()->back();
        }

        return redirect(\route('back.finance.invoices.index'));
    }

    function markUnderReview($invoice_id, Request $request)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $invoice->status = Invoice::STATUS_PENDING_CHECK;
        $invoice->under_review_reason = now()->setTimezone('Asia/Riyadh')->toDateString().' - '.$request->under_review_reason;
        $invoice->chased_by_id = auth()->user()->id;
        $invoice->save();
        return redirect()->route('back.finance.invoices.show', $invoice->id);
    }

    function markArchived($invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $invoice->status = Invoice::STATUS_ARCHIVED;
        $invoice->save();
        return redirect()->route('back.finance.invoices.show', $invoice->id);
    }

    function resetStatus($invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $invoice->status = Invoice::STATUS_UNPAID;
        $invoice->save();
        return redirect()->route('back.finance.invoices.show', $invoice->id);
    }

    public function getPaymentUrl(Invoice $invoice)
    {
        $url = (new NoonService())->createPaymentUrlForInvoice($invoice);
        return response()->json(['url' => $url]);
    }
}

