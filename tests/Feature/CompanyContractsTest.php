<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyContractsTest extends TestCase
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

        $this->company = Company::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);
    }

    public function test_adding_a_contract()
    {
        $contract = [
            'company_id' => $this->company->id,
            'reference_number' => (string) rand(),
            'contract_starts_at' => now()->toDateString(),
            'contract_ends_at' => now()->addMonth()->toDateString(),
            'contract_period_in_months' => 1,
            'auto_renewal' => true,
            'trainees_count' => rand(),
            'trainee_salary' => rand(),
            'trainer_cost' => rand(),
            'company_reimbursement' => rand(),
            'notes' => $this->faker->text,
        ];

        $this->actingAs($this->user)
            ->post(route('back.companies.contracts.store', ['company_id' => Company::find($contract['company_id'])]), $contract)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('company_contracts', $contract);
    }

    public function test_view_contracts_section()
    {
        $companyContract = $this->company->contracts()->save(
            CompanyContract::factory()->make(['team_id' => $this->company->team_id])
        );

        $this->actingAs($this->user)
            ->get(route('back.companies.show', $companyContract->company_id))
            ->assertPropValue('company', function($company) use ($companyContract) {
                $this->assertEquals($company['id'], $companyContract['company_id']);
            });
    }

    public function test_user_can_view_contract_create_form()
    {
        $this->actingAs($this->user)
            ->get(route('back.companies.contracts.create', ['company_id' => $this->company->id]))
            ->assertSuccessful()
            ->assertPropValue('company', function($company) {
                $this->assertEquals($company['id'], $this->company['id']);
            });
    }
}
