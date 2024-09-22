<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Services\FinancialService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateSubscriptionsTest extends TestCase
{
    use WithFaker;

    public function test_subscription_is_seeded_for_company()
    {
        $user = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $this->assertDatabaseHas('subscription_plans', [
            'team_id' => $user->personalTeam()->id,
            'amount' => FinancialService::DEFAULT_TRAINEE_MONTHLY_SUBSCRIPTION * 100, // Minor amount.
            'billing_interval' => 'monthly',
            'currency' => 'SAR',
        ]);
    }
}
