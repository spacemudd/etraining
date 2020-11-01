<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\FinancialSetting;
use Brick\Money\Money;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialMonthlySubscriptionController extends Controller
{
    /**
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit()
    {
        $this->authorize('view-financial-department');

        $financial = FinancialSetting::first();

        return Inertia::render('Back/Finance/MonthlySubscription/Edit', [
            'trainee_monthly_subscription' => $financial->trainee_monthly_subscription,
        ]);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'trainee_monthly_subscription' => 'required|integer|min:0',
        ]);

        $setting = FinancialSetting::firstOrFail();
        $setting->trainee_monthly_subscription = Money::of($request->trainee_monthly_subscription, 'SAR')
            ->getMinorAmount()
            ->toInt();
        $setting->save();

        return redirect()->route('back.finance');
    }
}
