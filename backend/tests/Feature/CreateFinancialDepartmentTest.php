<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Services\FinancialService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateFinancialDepartmentTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);
    }

    public function test_user_can_see_finance()
    {
        $this->actingAs($this->user)
            ->get(route('back.finance'))
            ->assertSuccessful();
    }

    public function test_user_can_see_clients_page()
    {
        $this->actingAs($this->user)
            ->get(route('back.finance.accounts.index'))
            ->assertSuccessful();
    }

    public function test_user_can_see_invoices_page()
    {
        $this->actingAs($this->user)
            ->get(route('back.finance.invoices.index'))
            ->assertSuccessful();
    }

    public function test_user_can_view_monthly_cost_of_subscription()
    {
        $this->actingAs($this->user)
            ->get(route('back.finance.monthly-subscription.edit'))
            ->assertSuccessful();
    }

    public function test_team_financal_settings_are_seeded()
    {
        // Settings are already seeded when this feature test called setUp().

        $this->assertDatabaseHas('financial_settings', [
            'team_id' => $this->user->personalTeam()->id,
            'trainee_monthly_subscription' => FinancialService::DEFAULT_TRAINEE_MONTHLY_SUBSCRIPTION * 100,
        ]);
    }

    public function test_updating_monthly_subscription()
    {
        $cost = 2000; // $2000.

        $this->actingAs($this->user)
            ->put(route('back.finance.monthly-subscription.update'), [
                'trainee_monthly_subscription' => 2000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('financial_settings', [
            'team_id' => $this->user->personalTeam()->id,
            'trainee_monthly_subscription' => $cost * 100, // Minor amount.
        ]);
    }
}
