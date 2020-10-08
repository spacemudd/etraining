<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyContractsTest extends TestCase
{
    use RefreshDatabase;
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
            'number' => (string) rand(),
            'date' => now()->format('d-m-Y'),
            'trainees_count' => rand(),
            'trainee_salary' => rand(),
            'trainer_cost' => rand(),
            'company_reimbursement' => rand(),
            'notes' => $this->faker->text,
        ];

        $this->actingAs($this->user)
            ->post(route('back.companies.contracts.store'), $contract)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('company_contracts', $contract);
    }
}
