<?php

namespace App\Http\Controllers\Trainees\Payment;

use App\Http\Controllers\Controller;
use App\Mail\EditAmountMail;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Audit;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Models\TraineeBankPaymentReceipt;
use App\Services\InvoiceService;
use CodeBugLab\NoonPayment\NoonPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Mail;

class PaymentCardController extends Controller
{
    /**
     * Redirect user to payment form.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function showPaymentForm(Request $request)
    {
        $invoice = Invoice::find($request->invoice_id);
        return redirect($this->getPaymentUrl($invoice));
    }

    /**
     * Confirm the order had been paid successfully.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function chargePayment(Request $request)
    {
        sleep(2);

        $order = NoonPayment::getInstance()->getOrder($request->orderId);

        if ($this->isNoonPaymentSuccess($order)) {
            session()->put('success_payment', true);
        } else {
            session()->put('failed_payment', true);
        }

        return redirect()
            ->route('dashboard');
    }

    /**
     * To Get the Payment URL.
     *
     * @param \App\Models\Back\Invoice $invoice
     * @return mixed|null
     */
    private function getPaymentUrl(Invoice $invoice)
    {
        $response = NoonPayment::getInstance()->initiate([
            'order' => [
                'reference' => $invoice->id,
                'amount' => $invoice->grand_total,
                'currency' => 'SAR',
                'name' => 'Trainee fees payment',
            ],
            'billing' => [
                'contact' => [
                    'firstName' => Str::before($invoice->trainee->name, ' '),
                    'lastName' => Str::afterLast($invoice->trainee->name, ' '),
                    'phone' => $invoice->trainee->clean_phone,
                    'email' => $invoice->trainee->email,
                ],
            ],
            'configuration' => [
                'locale' => 'ar',
                'returnUrl' => url(route('trainees.payment.card.charge')),
            ]
        ]);

        if ($response->resultCode === 0) {
            return $response->result->checkoutData->postUrl;
        } else {
            Log::error('Noon Payment Error', (array) $response);
            abort(422);
        }
    }

    /**
     * Receives Noon webhook and saves the receipt ID.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function storeNoonReceipt(Request $request)
    {
        $order = NoonPayment::getInstance()->getOrder($request->orderId);
        // Confirm that Noon has the payment.
        throw_if(!$order, 'Invoice not found in payment gateway');

        $invoice_id = $order->result->order->reference;

        if ($this->isNoonPaymentSuccess($order)) {
            DB::beginTransaction();
            Audit::create([
                'event' => 'noon',
                'auditable_id' => $invoice_id,
                'auditable_type' => Invoice::class,
                'new_values' => $request->toArray(),
            ]);

            $invoice = Invoice::withoutGlobalScopes()->notPaid()->with(['trainee' => function($q) {
                $q->withTrashed();
            }])->find($invoice_id);

            $invoice->update([
                'payment_method' => Invoice::PAYMENT_METHOD_CREDIT_CARD,
                'payment_reference_id' => $order->result->order->id,
                'paid_at' => now(),
                'status' => Invoice::STATUS_PAID,
            ]);

            AccountingLedgerBook::create([
                'team_id' => $invoice->company->team_id,
                'company_id' => $invoice->company_id,
                'trainee_id' => $invoice->trainee_id,
                'invoice_id' => $invoice->id,
                'date' => now(),
                'description' => $order->result->order->id,
                'reference'  => 'دفع عبر الموقع',
                'account_name' => $invoice->trainee->name,
                'credit' => $invoice->grand_total,
                'balance' => AccountingLedgerBook::getBalanceForTrainee($invoice->trainee->id) - $invoice->grand_total,
            ]);
            DB::commit();
        } else {
            $this->recordFailure($request, $order);
        }
    }

    public function recordFailure(Request $request, $order)
    {
        DB::beginTransaction();
        $invoice = Invoice::with([
            'trainee' => function ($q) {
                $q->withTrashed();
            }
        ])->find($order->result->order->reference);

        Audit::create([
            'team_id' => $invoice->trainee->team_id,
            'event' => 'payment_failure',
            'auditable_id' => $invoice->trainee->id,
            'auditable_type' => Trainee::class,
            'new_values' => $request->toArray(),
        ]);
        DB::commit();
    }

    public function showOptions()
    {
        $trainee = auth()->user()->trainee;

        $pending_amount = number_format($trainee->total_amount_owed, 2);

        return Inertia::render('Trainees/Payment/Index', [
            'pending_amount' => $pending_amount,
            'online_payment' => $trainee->team->online_payment,
            'invoices' => $trainee->invoices()->notPaid()->get(),
        ]);
    }
    public function chooseInvoice()
    {
        $trainee = auth()->user()->trainee;

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
        DB::commit();

        // TODO: Make this a permission. Then assign the permission to the role group in the app.
        Mail::to(['hadeel@ptc-ksa.net', 'hadeel.m@ptc-ksa.net', 'reem@ptc-ksa.net', 'shahad.m@ptc-ksa.net'])
            ->queue(new EditAmountMail($invoice));

        return $this->getPaymentUrl(Invoice::find($invoice->id));
    }

    private function isNoonPaymentSuccess($order)
    {
        return isset($order->result->transactions) &&
            is_array($order->result->transactions) &&
            $order->result->transactions[0]->type == "SALE" &&
            $order->result->transactions[0]->status == "SUCCESS";
    }
}
