<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
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

        DB::transaction(function () use ($trainee, $validatedData) {
            $invoice = $trainee->invoices()->create(
                array_merge([
                    'company_id' => $trainee->company->id,
                    'total_amount' => $validatedData['invoice_value'],
                ], $validatedData)
            );

            $period = [
                'start' => Carbon::parse($validatedData['from_date'])->format('jS F'),
                'end' => Carbon::parse($validatedData['to_date'])->format('jS F'),
            ];

            $invoice->items()->create([
                'name_en' => __('words.training-costs-for-the-period-of', $period, 'en'),
                'name_ar' => __('words.training-costs-for-the-period-of', $period, 'ar'),
                'amount' => $validatedData['invoice_value'],
                'tax' => round($validatedData['invoice_value'] * 0.15, 2),
            ]);
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
