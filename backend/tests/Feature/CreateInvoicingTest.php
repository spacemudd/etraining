<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use App\Models\Back\Trainee;
use App\Services\MonthlyInvoicingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateInvoicingTest extends TestCase
{
    use WithFaker;

    /**
     * An Admin user.
     *
     * @var \App\Models\User
     */
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $this->admin->current_team_id = $this->admin->personalTeam()->id;
        $this->admin->save();
    }

    public function test_monthly_invoicing_page_is_accessible()
    {
        $this->actingAs($this->admin)
            ->get(route('back.finance.invoicing.index'))
            ->assertSuccessful();
    }

    public function test_creating_a_monthly_invoicing_batch()
    {
        $this->acting($this->admin)
            ->post(route('back.finance.invoicing.create'))
            ->assertSuccessful();
    }

    public function test_monthly_training_fees_are_invoiced_to_trainees()
    {
        // Make one company to hold the contract.
        $company = Company::factory()->create([
            'team_id' => $this->admin->current_team_id,
        ]);

        // The contract to be assigned for the trainees.
        $contract = $company->contracts()->save(
            CompanyContract::factory()->make(['team_id' => $company->team_id])
        );

        // Make a trainee.
        $trainee = Trainee::factory()->create([
            'team_id' => $this->admin->current_team_id,
            'company_id' => $company->id,
            'company_contract_id' => $contract->id,
        ]);

        // We're gonna get all the trainees under the contract and do this month's billing.
        // Now, we're going to set the monthly invoicing period
        // Question: What is the period of the invoice? It is for the past period.
        // E.g. If a student joins in the middle of the month, then at the end of the month
        // They're charged alongside the other people in the contract.

        app()->make(MonthlyInvoicingService::class)
            ->generateTraineesMonthlyInvoices();
    }
}
