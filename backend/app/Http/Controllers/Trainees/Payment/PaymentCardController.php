<?php

namespace App\Http\Controllers\Trainees\Payment;

use App\Http\Controllers\Controller;
use App\Models\Back\Invoice;
use Brick\PhoneNumber\PhoneNumber;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
        $trainee = auth()->user()->trainee()->firstOrFail();

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
        $trainee = $user->trainee()->firstOrFail();

        $invoices = explode(",", json_decode($tap_invoice->getMetaData()['invoices']));
        $invoices = $trainee->invoices()->notPaid()->findMany($invoices);

        if ($invoices->count() === 0) {
            // Handle error logic, Invoices already paid or for some other reason not found
            abort(500);
        }

        if (!$tap_invoice->isSuccess()) {
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
            ]);
        });

        // Show success page
        return "success";
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

            if (!empty($trainee->phone)) {
                $phone = PhoneNumber::parse($trainee->phone);
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
}
