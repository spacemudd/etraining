<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\InvoiceItem;
use App\Models\Back\Trainee;
use App\Notifications\NewInvoiceIssued;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TraineeInvoicesController extends Controller
{
    public function create(string $trainee_id): Response
    {
        $this->authorize('issue-monthly-invoices');

        $trainee = Trainee::query()
            ->whereHas('company')
            ->with(['company'])
            ->findOrFail($trainee_id);

        return Inertia::render('Back/Trainees/Invoices/Create', [
            'trainee' => $trainee,
            'monthly_subscription_per_trainee' => $trainee->company->monthly_subscription_per_trainee,
        ]);
    }

    public function store(Request $request, string $trainee_id)
    {
        $this->authorize('issue-monthly-invoices');

        $trainee = Trainee::query()
            ->whereHas('company')
            ->with(['company'])
            ->findOrFail($trainee_id);

        $validatedData = $this->validateStoreRequest($request, $trainee->id);

        //$sub_total = Money::of($validatedData['invoice_value'], 'SAR', new CustomContext(2), RoundingMode::HALF_UP);
        //$tax = $sub_total->multipliedBy(InvoiceItem::DEFAULT_TAX, RoundingMode::HALF_UP);
        //$grand_total = $sub_total->plus($tax);

        $grand_total = Money::of($validatedData['invoice_value'], 'SAR', new CustomContext(2), RoundingMode::HALF_UP);
        $sub_total = $grand_total->multipliedBy(1 / (1 + InvoiceItem::DEFAULT_TAX), RoundingMode::HALF_UP);
        $tax = $grand_total->minus($sub_total);

        DB::transaction(function () use ($trainee, $validatedData, $grand_total, $tax, $sub_total) {
            $invoice = $trainee->invoices()->create(
                array_merge([
                    'company_id' => $trainee->company->id,
                    'sub_total' => $sub_total->getAmount()->toFloat(),
                    'tax' => $tax->getAmount()->toFloat(),
                    'grand_total' => $grand_total->getAmount()->toFloat(),
                ], $validatedData)
            );

            $period = [
                'start' => Carbon::parse($validatedData['from_date'])->format('Y-m-d'),
                'end' => Carbon::parse($validatedData['to_date'])->format('Y-m-d'),
            ];

            $invoice->items()->create([
                'name_en' => __('words.training-costs-for-the-period-of', $period, 'en'),
                'name_ar' => __('words.training-costs-for-the-period-of', $period, 'ar'),
                'sub_total' => $sub_total->getAmount()->toFloat(),
                'tax' => $tax->getAmount()->toFloat(),
                'grand_total' => $grand_total->getAmount()->toFloat(),
            ]);

            AccountingLedgerBook::create([
                'team_id' => $invoice->team_id,
                'company_id' => $invoice->company_id,
                'trainee_id' => $invoice->trainee_id,
                'invoice_id' => $invoice->id,
                'date' => now(),
                'description' => __('words.training-costs-for-the-period-of', $period, 'ar'),
                'reference'  => __('words.training-costs-for-the-period-of', $period, 'ar'),
                'account_name' => $invoice->trainee->name,
                'debit' => $invoice->grand_total,
                'balance' => AccountingLedgerBook::getBalanceForTrainee($invoice->trainee->id) + $invoice->grand_total,
            ]);

            // Trainee::find($invoice->trainee->id)->notify(new NewInvoiceIssued());
        });

        return redirect()->route('back.trainees.show', $trainee_id);
    }

    private function validateStoreRequest(Request $request, string $trainee_id): array
    {
        return $request->validate([
            'from_date' => [
                'required',
                'date',
                'before:to_date'
            ],
            'to_date' => [
                'required',
                'date',
                'after:from_date'
            ],
            'invoice_value' => [
                'required',
                'integer',
                "min:1",
                "max:10000000",
            ],
        ]);
    }
}
