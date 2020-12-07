<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
use App\Models\Team;
use App\Models\User;
use App\Services\RolesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTraineesTest extends TestCase
{
    public function test_trainee_can_see_signup_page()
    {
        $this->get(route('register.trainees'))
            ->assertSuccessful();
    }

    public function test_trainee_register()
    {
        // Make a new team.
        $admin = User::factory()->create();
        $team = $admin->ownedTeams()->create([
            'name' => 'eTraining Shafiq',
            'personal_team' => false,
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        $trainee = [
            'name' => 'Shafiq al-Shaar',
            'email' => 'shafiqalshaar@gmail.com',
            'identity_number' => '2020202010',
            'password' => 'password',
            'password_confirmation' => 'password',
            'birthday' => '1994-12-10',
            'phone' => '966565176235',
            'phone_additional' => '966565176235',
            'educational_level_id' => EducationalLevel::create(['order' => 1, 'name_en' => 'College_'])->id,
            'city_id' => City::create(['name' => 'Riyadh'])->id,
            'marital_status_id' => MaritalStatus::create(['order' => 1, 'name_en' => 'Single_'])->id,
            'children_count' => 0,
        ];

        $this->post(route('register.trainees'), $trainee)
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => $trainee['email'],
            'email_verified_at' => null,
        ]);

        $this->assertDatabaseHas('trainees', [
            'email' => $trainee['email'],
        ]);
    }

    // todo:
    public function test_trainee_redirected_to_completing_their_application()
    {
        // Make a new team.
        $admin = User::factory()->create();
        $team = $admin->ownedTeams()->create([
            'name' => 'eTraining Shafiq',
            'personal_team' => false,
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        $trainee = [
            'name' => 'Shafiq al-Shaar',
            'email' => 'shafiqalshaar@gmail.com',
            'identity_number' => '2020202010',
            'password' => 'password',
            'password_confirmation' => 'password',
            'birthday' => '1994-12-10',
            'phone' => '966565176235',
            'phone_additional' => '966565176235',
            'educational_level_id' => EducationalLevel::create(['order' => 1, 'name_en' => 'College_'])->id,
            'city_id' => City::create(['name' => 'Riyadh'])->id,
            'marital_status_id' => MaritalStatus::create(['order' => 1, 'name_en' => 'Single_'])->id,
            'children_count' => 0,
        ];

        $this->post(route('register.trainees'), $trainee);

        $this->actingAs(User::whereEmail($trainee['email'])->first())
            ->get(route('dashboard'))
            ->assertRedirect(route('trainees.application'));
    }
}
