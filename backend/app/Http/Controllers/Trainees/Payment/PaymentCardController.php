<?php

namespace App\Http\Controllers\Trainees\Payment;

use App\Http\Controllers\Controller;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Invoice;
use App\Models\TraineeBankPaymentReceipt;
use Brick\PhoneNumber\PhoneNumber;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;
use Tap\TapPayment\Facade\TapPayment;

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

        $pending_invoices_count = $trainee->invoices()->notPaid()->count();
        $pending_amount = optional($trainee)->total_amount_owed;

        if ($pending_invoices_count === 0 || $pending_amount === 0) {
            // Handle logic
            abort(404);
        }

        $payment_url = $this->getPaymentUrl($pending_amount);

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
        if (!$request->has('tap_id')) {
            abort(404);
        }

        try {
            $tap_invoice = TapPayment::findCharge($request->input('tap_id'));
        } catch (Exception $exception) {
            // your handling of request failure
            // dd($exception->getMessage());
            abort(404);
        }

        $user = auth()->user();
        $trainee = $user->trainee;

        $invoices = explode(",", json_decode($tap_invoice->getMetaData()['invoices']));
        $invoices = $trainee->invoices()->notPaid()->findMany($invoices);

        if ($invoices->count() === 0) {
            // Handle error logic, Invoices already paid or for some other reason not found
            abort(500);
        }

        if (!$tap_invoice->isSuccess()) {
            \Log::error(['msg' => 'A CC payment failed', 'trainee_id' => auth()->user()->trainee->id, collect($tap_invoice)]);
            return redirect()->route('dashboard');
            //if ($tap_invoice->status);
            // dd($tap_invoice->status); // DECLINED,
            // Handle error logic, Payment failed
            abort(500);
        }

        // If we reach this point, it means payment is successful.
        $invoices->each(function ($invoice) use ($tap_invoice) {
            $invoice->update([
                'payment_method' => Invoice::PAYMENT_METHOD_CREDIT_CARD,
                'payment_reference_id' => $tap_invoice->getId(),
                'paid_at' => now(),
                'status' => Invoice::STATUS_PAID,
            ]);

            AccountingLedgerBook::create([
                'team_id' => $invoice->team_id,
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
        });

        // Show success page
        return redirect()->route('dashboard');
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
    private function getPaymentUrl($amount)
    {
        $payment_url = null;

        try {
            $trainee = optional(auth()->user())->trainee;

            $payment = TapPayment::createCharge();
            $payment->setCustomerName($trainee->name);
            $payment->setDescription("Pending dues");
            $payment->setAmount($amount);
            $payment->setCurrency("SAR");
            $payment->setSource("src_card");

            if (!empty($trainee->clean_phone)) {
                $phone = PhoneNumber::parse('+'.$trainee->clean_phone);
                if ($phone->isPossibleNumber()) {
                    $payment->setCustomerPhone($phone->getCountryCode(), $phone->getNationalNumber());
                }
            }

            $payment->setRedirectUrl(url(route('trainees.payment.card.charge')));
//            $payment->setPostUrl(url(route('trainees.payment.card.charge'))); // if you are using post request to handle payment updates

            $payment->setMetaData([
                'invoices' => json_encode($trainee->invoices()->notPaid()->pluck('id')->implode(',')),
            ]);

            $invoice = $payment->pay();
            $payment_url = $invoice->getPaymetUrl();
        } catch (Exception $exception) {
            // Your handling of request failure
            throw $exception;
        }

        return $payment_url;
    }

    public function showOptions()
    {
        $trainee = auth()->user()->trainee;

        $pending_invoices_count = $trainee->invoices()->notPaid()->count();
        $pending_amount = number_format($trainee->total_amount_owed, 2);

        return Inertia::render('Trainees/Payment/Index', [
            'pending_amount' => $pending_amount,
            'online_payment' => $trainee->team->online_payment,
        ]);
    }

    public function uploadReceipt()
    {
        $pending_amount = auth()->user()->trainee->total_amount_owed;
        return Inertia::render('Trainees/Payment/UploadReceipt', [
            'pending_amount' =>  number_format($pending_amount, 2),
            'pending_amount_raw' => $pending_amount,
            'trainee' => auth()->user()->trainee,
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
            ->notPaid()
            ->get();

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
}
