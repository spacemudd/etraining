<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CompaniesTest extends TestCase
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

    public function test_user_can_view_company_form()
    {

    }
}
