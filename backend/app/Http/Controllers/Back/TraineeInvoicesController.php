<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;
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

        $validatedData = $request->validate([
            'month' => [
                'required',
                'numeric',
                'min:1',
                'max:12',
                Rule::unique('invoices', 'month')
                    ->where('year', now()->year)
                    ->where('trainee_id', $trainee->id),
            ],
            'year' => [
                'required',
                'numeric',
                'min:2021',
                'max:' . (now()->addYear()->year),
            ],
        ]);

        $invoice = $trainee->invoices()->create(
            array_merge([
                'company_id' => $trainee->company->id,
                'amount' => $trainee->company->monthly_subscription_per_trainee,
            ], $validatedData)
        );

        return redirect()->route('back.trainees.show', $trainee_id);
    }
}
