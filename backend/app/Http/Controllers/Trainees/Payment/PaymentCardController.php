<?php

namespace App\Http\Controllers\Trainees\Payment;

use App\Exceptions\NoonUnmatchedPaymentException;
use App\Http\Controllers\Controller;
use App\Mail\EditAmountMail;
use App\Mail\NoonUnmatchedPaymentMail;
use App\Mail\RejectNewEmailMail;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Audit;
use App\Models\Back\Invoice;
use App\Models\PaymentOutageInterest;
use App\Models\TraineeBankPaymentReceipt;
use App\Services\InvoiceService;
use App\Services\NoonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Throwable;

class PaymentCardController extends Controller
{
    private $paymentService;

    public function __construct(NoonService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Redirect user to payment form, or show the outage notice when Noon is unavailable.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function showPaymentForm(Request $request)
    {
        $trainee = optional(auth()->user())->trainee;
        $invoice = Invoice::notPaid()->find($request->invoice_id);

        if ($this->isCardPaymentUnavailable()) {
            PaymentOutageInterest::remember($trainee, $invoice);

            return $this->paymentGatewayUnavailableView();
        }

        if (! $invoice) {
            abort(404);
        }

        try {
            $url = $this->paymentService->createPaymentUrlForInvoice($invoice);

            return redirect($url);
        } catch (Throwable $e) {
            PaymentOutageInterest::remember($trainee, $invoice);
            Log::error('Noon payment URL failed', [
                'message' => $e->getMessage(),
                'invoice_id' => $invoice->id,
            ]);

            return $this->paymentGatewayUnavailableView();
        }
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

        $success = $this->paymentService->isOrderSuccessful($request->orderId, $request->centerId);

        if ($success) {
            session()->put('success_payment', true);
        } else {
            session()->put('failed_payment', true);
        }

        return redirect()
            ->route('dashboard');
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
        $order = $this->paymentService->getOrder($request->orderId,5676); // try finding the order in Jasarah
        if (is_null($order) || $order->resultCode === 5021 || $order->resultCode === 19089 || $order->resultCode === 19001) { // 5021 is bad request in Noon (not found in Jasarah)
            $order = $this->paymentService->getOrder($request->orderId, 0); // try finding the order in Jisr
        }

        // Confirm that Noon has the payment.
        throw_if(!$order, 'Invoice not found in payment gateway');

        if ($this->paymentService->isPaymentSuccess($order)) {
            $invoice_id = $order->result->order->reference;
            Audit::create([
                'event' => 'noon',
                'auditable_id' => $invoice_id,
                'auditable_type' => Invoice::class,
                'new_values' => $request->toArray(),
            ]);

            DB::beginTransaction();

            $invoice = Invoice::withoutGlobalScopes()
                ->withTrashed()
                ->with(['trainee' => function($q) {
                    $q->withTrashed();
                }])->find($invoice_id);

            if (!$invoice) {
                DB::rollBack();
                $this->alertUnmatchedNoonPayment($request, $order, 'invoice_not_found', $invoice_id);

                return 1;
            }

            if (!$invoice->trainee) {
                DB::rollBack();
                $this->alertUnmatchedNoonPayment($request, $order, 'trainee_missing', $invoice->id);

                return 1;
            }

            // TODO: If the invoice is deleted, notify the admins via email.
            if ($invoice->deleted_at) {
                $invoice->restore();
            }

            $invoice->update([
                'payment_method' => Invoice::PAYMENT_METHOD_CREDIT_CARD,
                'payment_reference_id' => $order->result->order->id,
                'paid_at' => now(),
                'status' => Invoice::STATUS_PAID,
                'payment_detail_method' => $order->result->paymentDetails->mode,
                'payment_detail_brand' => $order->result->paymentDetails->brand,
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

        return 1;
    }

    public function recordFailure(Request $request, $order)
    {
        DB::beginTransaction();
        $invoiceReference = $order->result->order->reference ?? null;
        $invoice = Invoice::withTrashed()
            ->with([
                'trainee' => function ($q) {
                    $q->withTrashed();
                }
            ])->find($invoiceReference);

        if (!$invoice || !$invoice->trainee) {
            Log::warning('Noon payment failure webhook could not resolve invoice/trainee', [
                'order_id' => $request->orderId,
                'invoice_reference' => $invoiceReference,
                'has_invoice' => (bool) $invoice,
            ]);
            DB::rollBack();

            return;
        }

        Audit::create([
            'team_id' => $invoice->trainee->team_id,
            'event' => 'payment_failure',
            'auditable_id' => $invoice->trainee->id,
            'auditable_type' => Trainee::class,
            'new_values' => $request->toArray(),
        ]);
        DB::commit();
    }

    /**
     * Escalate a successful Noon payment that cannot be matched to an invoice/trainee.
     *
     * @param object $order
     */
    private function alertUnmatchedNoonPayment(Request $request, $order, string $reason, ?string $invoiceReference): void
    {
        $context = [
            'reason' => $reason,
            'order_id' => $request->orderId,
            'invoice_reference' => $invoiceReference,
            'noon_order_id' => $order->result->order->id ?? null,
            'amount' => $order->result->order->amount ?? null,
            'currency' => $order->result->order->currency ?? null,
            'payment_status' => $order->result->order->status ?? null,
            'received_at' => now()->toDateTimeString(),
            'webhook_payload' => $request->toArray(),
        ];

        Log::error('Noon successful payment could not be matched to invoice/trainee', $context);

        try {
            Audit::create([
                'event' => 'noon_unmatched_payment',
                'auditable_id' => $invoiceReference,
                'auditable_type' => Invoice::class,
                'new_values' => $context,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to store noon_unmatched_payment audit', [
                'error' => $e->getMessage(),
                'context' => $context,
            ]);
        }

        $exception = new NoonUnmatchedPaymentException(
            sprintf(
                'Noon payment succeeded but could not be matched (%s). orderId=%s reference=%s noonOrderId=%s amount=%s',
                $reason,
                $context['order_id'] ?? 'n/a',
                $context['invoice_reference'] ?? 'n/a',
                $context['noon_order_id'] ?? 'n/a',
                $context['amount'] ?? 'n/a'
            )
        );

        if (app()->bound('sentry')) {
            try {
                app('sentry')->withScope(function (\Sentry\State\Scope $scope) use ($exception, $context): void {
                    $scope->setLevel(\Sentry\Severity::error());
                    $scope->setTag('payment_provider', 'noon');
                    $scope->setTag('payment_issue', 'unmatched_success');
                    $scope->setContext('noon_unmatched_payment', $context);
                    app('sentry')->captureException($exception);
                });
            } catch (Throwable $e) {
                Log::error('Failed to report unmatched Noon payment to Sentry', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        try {
            Mail::to([
                'sara@hadaf-hq.com',
                'shafiqalshaar@adv-line.com',
            ])->send(new NoonUnmatchedPaymentMail($context));
        } catch (Throwable $e) {
            Log::error('Failed to email finance about unmatched Noon payment', [
                'error' => $e->getMessage(),
                'context' => $context,
            ]);
        }
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

        if ($this->isCardPaymentUnavailable()) {
            PaymentOutageInterest::remember($trainee);

            return $this->paymentGatewayUnavailableView();
        }

        $pending_amount = number_format($trainee->total_amount_owed, 2);

        return Inertia::render('Trainees/Payment/IndexTap', [
            'pending_amount' => $pending_amount,
            'online_payment' => $trainee->team->online_payment,
            'invoices' => $trainee->invoices()->notPaid()->get(),
        ]);
    }

    private function isCardPaymentUnavailable(): bool
    {
        return (bool) config('payment.gateway_unavailable');
    }

    private function paymentGatewayUnavailableView()
    {
        return response()->view('trainees.payment.gateway-unavailable');
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

    /**
     * @throws \Throwable
     * @throws \Brick\Money\Exception\MoneyMismatchException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     */
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

        return $this->paymentService->createPaymentUrlForInvoice($invoice); // redirect is done by frontend js.
    }
}
