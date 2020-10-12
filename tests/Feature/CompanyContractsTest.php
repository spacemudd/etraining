<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use App\Models\Back\Instructor;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Storage;
use Illuminate\Http\UploadedFile;

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
            'instructor_cost' => rand(),
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

    public function test_uploading_pdf_with_contract()
    {
        Storage::fake('s3');

        $contract = [
            'company_id' => $this->company->id,
            'reference_number' => (string) rand(),
            'contract_starts_at' => now()->toDateString(),
            'contract_ends_at' => now()->addMonth()->toDateString(),
            'contract_period_in_months' => 1,
            'auto_renewal' => true,
            'trainees_count' => rand(),
            'trainee_salary' => rand(),
            'instructor_cost' => rand(),
            'company_reimbursement' => rand(),
            'notes' => $this->faker->text,
            'files' => [
                $file = UploadedFile::fake()->create('contract-copy.pdf', 1024 * 24),
            ],
        ];

        $this->actingAs($this->user)
            ->post(route('back.companies.contracts.store', ['company_id' => Company::find($contract['company_id'])]), $contract)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $contract_path = CompanyContract::where('company_id', $contract['company_id'])
            ->first()
            ->getFirstMedia('contract_copy')
            ->getPath();

        $this->assertFileExists($contract_path);
    }

    /**
     * TODO: Not sure how to test S3 using Storage::fake();
     *
     */
    //public function test_downloading_contract_files()
    //{
    //    Storage::fake('s3');
    //
    //    $contract = CompanyContract::factory()->create([
    //        'company_id' => $this->company->id,
    //        'team_id' => $this->user->personalTeam()->id,
    //    ]);
    //
    //    $contract = CompanyContract::findOrFail($contract->id);
    //
    //    $file = UploadedFile::fake()->create('contract-copy.pdf', 1024 * 24);
    //    $contract->addContractCopyAttachment($file);
    //
    //    $this->actingAs($this->user)
    //        ->get(route('back.companies.contracts.attachments', ['company_id' => $contract->company_id, 'contract_id' => $contract->id]));
    //}

    public function test_attaching_instructor_to_contract()
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
            'instructor_cost' => rand(),
            'company_reimbursement' => rand(),
            'notes' => $this->faker->text,
        ];

        $this->actingAs($this->user)
            ->post(route('back.companies.contracts.store', ['company_id' => Company::find($contract['company_id'])]), $contract);

        $instructor = Instructor::factory()->create(['team_id' => $this->company->team_id]);

        $this->actingAs($this->user)
            ->put(route('back.companies.contracts.update', ['company_id' => $this->company->id, 'contract_id' => $contract->id]), ['instructor_id' => $instructor->id])
            ->assertRedirect();

        $this->assertDatabaseHas('company_contracts', [
            'id' => $contract->id,
            'instructor_id' => $instructor->id,
        ]);
    }
}
