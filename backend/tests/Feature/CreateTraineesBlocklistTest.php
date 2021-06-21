<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTraineesBlocklistTest extends TestCase
{
    use WithFaker;

    public function test_blocking_adding_a_name_and_id_to_trainees_list()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'admin@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $acmeCompany = Company::factory()->create(['team_id' => $admin->personalTeam()->id]);

        $trainee = Trainee::factory()->make([
            'team_id' => $admin->personalTeam()->id,
            'company_id' => $acmeCompany->id,
        ]);

        $this->actingAs($admin)->post(route('back.trainees.block-list.store'), [
            'name' => $trainee->name,
            'identity_number' => $trainee->identity_number,
            'phone' => $trainee->phone,
            'phone_additional' => $trainee->phone_additional,
            'reason' => 'No payments',
        ])->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('trainee_block_lists', [
            'name' => $trainee->name,
            'identity_number' => $trainee->identity_number,
            'phone' => $trainee->phone,
            'phone_additional' => $trainee->phone_additional,
            'reason' => 'No payments',
        ]);
    }

    public function test_blocking_an_existing_trainee()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'admin@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $acmeCompany = Company::factory()->create(['team_id' => $admin->personalTeam()->id]);

        $trainee = Trainee::factory()->create([
            'team_id' => $admin->personalTeam()->id,
            'company_id' => $acmeCompany->id,
        ]);

        $reason = $this->faker()->text(100);
        $this->actingAs($admin)
            ->post(route('back.trainees.suspend.store', ['trainee_id' => $trainee->id]), [
                'reason' => $reason,
            ])
            ->assertRedirect()
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('trainee_block_lists', [
            'trainee_id' => $trainee->id,
            'name' => $trainee->name,
            'email' => $trainee->email,
            'phone' => $trainee->phone,
            'phone_additional' => $trainee->phone_additional,
            'identity_number' => $trainee->identity_number,
            'reason' => $reason,
        ]);

        $this->assertDatabaseHas('trainees', [
            'id' => $trainee->id,
            'suspended_at' => now()->setSecond(0)->toDateTimeString(),
        ]);
    }

    // TODO: Add test for blocking a trainee from logging in.
}
