<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;
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
}
