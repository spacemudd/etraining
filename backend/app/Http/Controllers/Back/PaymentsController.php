<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentsController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Finance/Payments/Index', [
            'payments' => Payment::with('sale_invoice')->paginate(10),
        ]);
    }

    public function show($payment_id)
    {
        $this->authorize('view-payments');
        $payment = Payment::findOrFail($payment_id);
        return Inertia::render('Back/Finance/Payments/Show', [
            'payment' => $payment,
        ]);
    }
}
