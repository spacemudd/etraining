<?php
/**
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Services;

use App\Models\Back\FinancialSetting;
use App\Models\Back\SubscriptionPlan;
use App\Models\Team;
use Brick\Money\Money;

class FinancialService
{
    public const DEFAULT_TRAINEE_MONTHLY_SUBSCRIPTION = 1500;

    /**
     *
     * @param \App\Models\Team $team
     */
    public function seedSettingsToTeam(Team $team): void
    {
        $monthlyCost = new FinancialSetting();
        $monthlyCost->team_id = $team->id;
        $monthlyCost->trainee_monthly_subscription = Money::of(self::DEFAULT_TRAINEE_MONTHLY_SUBSCRIPTION, 'SAR')
            ->getMinorAmount()
            ->toInt();
        $monthlyCost->save();

        // Create default subscription.
        $subscription = new SubscriptionPlan();
        $subscription->team_id = $team->id;
        $subscription->amount = Money::of(self::DEFAULT_TRAINEE_MONTHLY_SUBSCRIPTION, 'SAR')
            ->getMinorAmount()
            ->toInt();
        $subscription->billing_interval = 'monthly';
        $subscription->currency = 'SAR';
        $subscription->save();
    }
}
