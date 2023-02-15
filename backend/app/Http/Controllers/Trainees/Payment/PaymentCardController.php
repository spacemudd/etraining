<?php

namespace App\Http\Controllers\Trainees\Payment;

use App\Http\Controllers\Controller;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Audit;
use App\Models\Back\Invoice;
use App\Models\Back\InvoiceItem;
use App\Models\Back\Trainee;
use App\Models\TraineeBankPaymentReceipt;
use App\Services\CompaniesAssignedToRiyadhBank;
use App\Services\InvoiceService;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Brick\PhoneNumber\PhoneNumber;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Inertia\Inertia;
use Tap\TapPayment\Facade\TapPayment;
use Tap\TapPayment\TapService;

class PaymentCardController extends Controller
{
    /**
     * Show Payment Form
     *
     * @param Request $request
     *
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function showPaymentForm(Request $request): RedirectResponse
    {
        $trainee = auth()->user()->trainee;

        if (request()->invoice_id) {
           $invoices =  $trainee->invoices()->where('id', request()->invoice_id)->notPaid();
        } else {
            $invoices = $trainee->invoices()->notPaid();
        }

        $pending_invoices_count = $invoices->count();
        $pending_amount = $invoices->sum('grand_total');

        if ($pending_invoices_count === 0 || $pending_amount === 0) {
            // Handle logic
            abort(404);
        }

        $payment_url = $this->getPaymentUrl($pending_amount, $invoices);

        if (empty($payment_url)) {
            abort(404);
        }

        return redirect($payment_url);
    }

    /**
     * Show Payment Form
     *
     * @param Request $request
     *
     * @return RedirectResponse|View
     */
    public function chargePayment(Request $request)
    {
        sleep(2);

        try {
            $tap_service = new TapService();
            $tap_invoice = $tap_service->findCharge($request->tap_id);
        } catch (\Exception $exception) {
            app()->make(CompaniesAssignedToRiyadhBank::class)
                ->setSecondaryTap();
            $tap_service = new TapService();
            $tap_invoice = $tap_service->findCharge($request->tap_id);
        }

        if ($tap_invoice->isSuccess()) {
            session()->put('success_payment', true);
        } else {
            session()->put('failed_payment', true);
        }

        // Show success page
        return redirect()
            ->route('dashboard');
    }

    /**
     * To Get the Payment URL.
     *
     * @param $amount
     *
     * @return mixed|null
     * @throws GuzzleException
     * @throws Exception
     */
    private function getPaymentUrl($amount, $invoices)
    {
        try {
            $trainee = optional(auth()->user())->trainee;
            app()->make(CompaniesAssignedToRiyadhBank::class)
                ->setTapKey($trainee->company_id);

            $payment = TapPayment::createCharge();
            $payment->setCustomerName($trainee->name);
            $payment->setDescription("Training Fees - رسوم التدريب");
            $payment->setAmount($amount);
            $payment->setCurrency("SAR");
            $payment->setSource("src_card");

            if (!empty($trainee->clean_phone)) {
                $phone = PhoneNumber::parse('+'.$trainee->clean_phone);
                if ($phone->isPossibleNumber()) {
                    $payment->setCustomerPhone($phone->getCountryCode(), $phone->getNationalNumber());
                } else {
                    $payment->setCustomerPhone('966', '553139979');
                }
            } else {
                $payment->setCustomerPhone('966', '553139979');
            }

            $payment->setRedirectUrl(url(route('trainees.payment.card.charge')));
            $payment->setPostUrl('https://prod.ptc-ksa.com/tap'); // TODO: create tap.ptc-ksa.com dedicated instance.

            $payment->setMetaData([
                'invoices' => json_encode($invoices->pluck('id')->implode(',')),
            ]);

            $invoice = $payment->pay();
            $payment_url = $invoice->getPaymetUrl();
        } catch (Exception $exception) {
            // Your handling of request failure
            throw $exception;
        }

        return $payment_url;
    }

    /**
     * Receives Tap webhook and saves the receipt ID.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function storeTapReceipt(Request $request)
    {
        $status = $request->status;

        $invoice_ids = explode(',', json_decode($request->metadata['invoices']));
        $invoice = Invoice::withTrashed()->find($invoice_ids[0]);
        app()->make(CompaniesAssignedToRiyadhBank::class)
            ->setTapKey($invoice->company_id);

        if ($status === 'CAPTURED') {
            // Confirm that Tap has the payment.
            $tap_service = new TapService();
            $tap_invoice = $tap_service->findCharge($request->id);
            throw_if(! $tap_invoice, 'Invoice not found in payment gateway');

            $invoice_ids = explode(',', json_decode($request->metadata['invoices']));

            DB::beginTransaction();
            foreach ($invoice_ids as $invoice_id) {
                Audit::create([
                    'event' => 'tap',
                    'auditable_id' => $invoice_id,
                    'auditable_type' => Invoice::class,
                    'new_values' => $request->toArray(),
                ]);
                $invoice = Invoice::notPaid()->with(['trainee' => function($q) {
                    $q->withTrashed();
                }])->find($invoice_id);

                $invoice->update([
                    'payment_method' => Invoice::PAYMENT_METHOD_CREDIT_CARD,
                    'payment_reference_id' => $tap_invoice->getId(),
                    'paid_at' => now(),
                    'status' => Invoice::STATUS_PAID,
                ]);

                AccountingLedgerBook::create([
                    'team_id' => $invoice->company->team_id,
                    'company_id' => $invoice->company_id,
                    'trainee_id' => $invoice->trainee_id,
                    'invoice_id' => $invoice->id,
                    'date' => now(),
                    'description' => $tap_invoice->getId(),
                    'reference'  => 'دفع عبر الموقع',
                    'account_name' => $invoice->trainee->name,
                    'credit' => $invoice->grand_total,
                    'balance' => AccountingLedgerBook::getBalanceForTrainee($invoice->trainee->id) - $invoice->grand_total,
                ]);
            }
            DB::commit();
        } else {
            $this->recordFailure($request);
        }
    }

    public function recordFailure(Request $request)
    {
        $invoice_ids = explode(',', json_decode($request->metadata['invoices']));

        DB::beginTransaction();
        foreach ($invoice_ids as $invoice_id) {
            $invoice = Invoice::with([
                'trainee' => function ($q) {
                    $q->withTrashed();
                }
            ])->find($invoice_id);

            Audit::create([
                'team_id' => $invoice->trainee->team_id,
                'event' => 'payment_failure',
                'auditable_id' => $invoice->trainee->id,
                'auditable_type' => Trainee::class,
                'new_values' => $request->toArray(),
            ]);
        }
        DB::commit();
    }

    public function showOptions()
    {
        $trainee = auth()->user()->trainee;

        $pending_invoices_count = $trainee->invoices()->notPaid()->count();
        $pending_amount = number_format($trainee->total_amount_owed, 2);

        return Inertia::render('Trainees/Payment/Index', [
            'pending_amount' => $pending_amount,
            'online_payment' => $trainee->team->online_payment,
            'invoices' => $trainee->invoices()->notPaid()->get(),
        ]);
    }
    public function showTap()
    {
        $trainee = auth()->user()->trainee;

        $pending_invoices_count = $trainee->invoices()->notPaid()->count();
        $pending_amount = number_format($trainee->total_amount_owed, 2);

        return Inertia::render('Trainees/Payment/IndexTap', [
            'pending_amount' => $pending_amount,
            'online_payment' => $trainee->team->online_payment,
            'invoices' => $trainee->invoices()->notPaid()->get(),
        ]);
    }

    public function objectionOfAmount(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        return Inertia::render('Trainees/Payment/ObjectionOfAmount', [
            'invoice' => $invoice,
        ]);
    }

    public function uploadReceipt()
    {
        if (request()->invoice_id) {
            $invoice = Invoice::findOrFail(request()->invoice_id);
          $pending_amount = $invoice->grand_total;
        } else {
            $invoice = null;
            $pending_amount = auth()->user()->trainee->total_amount_owed;
        }

        return Inertia::render('Trainees/Payment/UploadReceipt', [
            'pending_amount' =>  number_format($pending_amount, 2),
            'pending_amount_raw' => $pending_amount,
            'trainee' => auth()->user()->trainee,
            'invoice' => $invoice,
        ]);
    }

    public function storeReceipt(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'sender_name' => 'required|string|max:255|min:3',
            'bank_name_to' => 'required|string|max:255|min:5',
            'bank_name_from' => 'required|string|max:255|min:5',
            'files.*' => 'required|file',
            'invoice_id' => 'nullable',
        ]);

        \DB::beginTransaction();
        $receipt = new TraineeBankPaymentReceipt();
        $receipt->trainee_id = auth()->user()->trainee->id;
        $receipt->amount = $request->amount;
        $receipt->sender_name = $request->sender_name;
        $receipt->bank_from = $request->bank_name_from;
        $receipt->bank_to = $request->bank_name_to;
        $receipt->uploaded_by_id = auth()->user()->id;
        $receipt->save();

        foreach ($request->file('files', []) as $key => $file) {
            $receipt->uploadToFolder($file, 'receipts');
        }

        $invoices = auth()->user()
            ->trainee
            ->invoices()
            ->notPaid();

        if (request()->invoice_id) {
            $invoices = $invoices->where('id', request()->invoice_id);
        }

        $invoices = $invoices->get();

        $invoices->each(function ($invoice) use ($receipt) {
            $invoice->update([
                'payment_method' => Invoice::PAYMENT_METHOD_BANK_RECEIPT,
                'trainee_bank_payment_receipt_id' => $receipt->id,
                'paid_at' => now(),
                'status' => Invoice::STATUS_AUDIT_REQUIRED,
            ]);
        });
        \DB::commit();

        return redirect()->route('dashboard');
    }

    public function changeInvoiceAmountRedirectToPaymentGateway(Request $request)
    {
        $request->validate([
            'grand_total_override' => 'required|min:1|numeric',
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        DB::beginTransaction();

        $invoice = app()->make(InvoiceService::class)
            ->changeInvoiceCost($request->invoice_id, $request->grand_total_override);
;
        // Get collection of invoices because getPaymentUrl() expects a collection
        $invoices = Invoice::where('id', $invoice->id)->get();
        $payment_url = $this->getPaymentUrl($request->grand_total_override, $invoices);

        DB::commit();

        return redirect()->to($payment_url);
    }
}
