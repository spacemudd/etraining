<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use App\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompaniesTest extends TestCase
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

    public function test_user_can_create_company()
    {
        $acmeCompany = [
            'name_ar' => 'شفيق الشعار',
            'name_en' => 'Shafiq al-Shaar',
            'cr_number' => $this->faker->randomNumber(),
            'contact_number' => $this->faker->phoneNumber,
            'company_rep' => $this->faker->name,
            'company_rep_mobile' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'email' => $this->faker->companyEmail,
        ];

        $this->actingAs($this->user)
            ->post('/back/companies', $acmeCompany)
            ->assertSessionHasNoErrors()
            ->assertRedirect();
    }

    public function test_user_can_read_company_data()
    {
        $acmeCompany = Company::factory()->create(['team_id' => $this->user->personalTeam()->id]);
        $this->actingAs($this->user)
            ->get('/back/companies/'.$acmeCompany->id)
            ->assertSuccessful()
            ->assertPropValue('company', function($company) use ($acmeCompany) {
                $this->assertEquals($acmeCompany['name_ar'], $company['name_ar']);
                $this->assertEquals($acmeCompany['name_en'], $company['name_en']);
                $this->assertEquals($acmeCompany['cr_number'], $company['cr_number']);
                $this->assertEquals($acmeCompany['contact_number'], $company['contact_number']);
                $this->assertEquals($acmeCompany['company_rep'], $company['company_rep']);
                $this->assertEquals($acmeCompany['company_rep_mobile'], $company['company_rep_mobile']);
                $this->assertEquals($acmeCompany['address'], $company['address']);
                $this->assertEquals($acmeCompany['email'], $company['email']);
            });
    }

    public function test_roles_are_seeded_to_team()
    {
        $shafiq = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello1@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $this->assertTrue($shafiq->hasRole(Role::findByName($shafiq->personalTeam()->id.'_admins')));
    }

    public function test_user_can_view_company_create_form()
    {
        $this->actingAs($this->user)
            ->get('/back/companies/create')
            ->assertSuccessful();
    }

    public function test_admin_can_see_trainees_under_company()
    {
        $acmeCompany = Company::factory()->create(['team_id' => $this->user->personalTeam()->id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'company_id' => $acmeCompany->id,
        ]);
        $this->actingAs($this->user)
            ->get('/back/companies/'.$acmeCompany->id)
            ->assertSuccessful()
            ->assertPropValue('company', function($company) use ($acmeCompany) {
                $this->assertCount(1, $company['trainees']);
            });
    }
}
