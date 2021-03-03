<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
use App\Models\User;
use App\Services\RolesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TraineesGroupManagementTest extends TestCase
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

    public function make_trainee()
    {
        // Make a new team.
        $admin = User::factory()->create();
        $team = $admin->ownedTeams()->create([
            'name' => 'eTraining Shafiq',
            'personal_team' => false,
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        return [
            'name' => 'Davy Jones',
            'email' => 'davy.jones@gmail.com',
            'identity_number' => '2020202010',
            'birthday' => '1994-12-10',
            'phone' => '966565176235',
            'phone_additional' => '966565176235',
            'educational_level_id' => EducationalLevel::factory()->create()->id,
            'city_id' => City::create(['name' => 'Riyadh'])->id,
            'marital_status_id' => MaritalStatus::factory()->create()->id,
            'password' => 'password',
            'password_confirmation' => 'password',
            'children_count' => 1,
        ];
    }

    public function test_assigning_a_group_to_trainee()
    {
        $nancy = $this->user;

        $company = Company::factory()->create(['team_id' => $nancy->personalTeam()->id]);

        $teamX = TraineeGroup::factory()->create(['company_id' => $company->id, 'team_id' => $company->team_id]);

        $trainee = [
            'trainee_group_name' => $teamX->name,
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getshafiq',
            'identity_number' => '10000',
            'phone' => '96670000000',
            'phone_additional' => '97300000000',
            'birthday' => '1994-12-10',
            'educational_level_id' => null,
            'city_id' => null,
            'marital_status_id' => null,
            'children_count' => 0,
        ];

        $this->actingAs($this->user)
            ->post(route('back.trainees.store'), $trainee);

        $this->assertEquals(1, $teamX->trainees()->count());
        $this->assertEquals(1, Trainee::whereEmail($trainee['email'])->first()->trainee_group()->count());
    }

    public function test_trainee_cant_have_multiple_trainee_groups()
    {
        $nancy = $this->user;

        $company = Company::factory()->create(['team_id' => $nancy->personalTeam()->id]);

        $teamX = TraineeGroup::factory()->create([
            'company_id' => $company->id,
            'team_id' => $company->team_id,
            'name' => 'Team X',
        ]);

        $trainee = [
            'trainee_group_name' => $teamX->name,
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getshafiq',
            'identity_number' => '10000',
            'phone' => '96670000000',
            'phone_additional' => '97300000000',
            'birthday' => '1994-12-10',
            'educational_level_id' => null,
            'city_id' => null,
            'marital_status_id' => null,
            'children_count' => 0,
        ];

        $this->actingAs($this->user)
            ->post(route('back.trainees.store'), $trainee);

        $differentTeam = TraineeGroup::factory()->create([
            'company_id' => $company->id,
            'team_id' => $company->team_id,
            'name' => 'Team Y',
        ]);
        $traineeDb = Trainee::whereEmail($trainee['email'])->first();
        $trainee['trainee_group_name'] = $differentTeam->name;
        $this->actingAs($this->user)
            ->put(route('back.trainees.update', $traineeDb->id), $trainee)
            ->assertSessionDoesntHaveErrors();

        $this->assertEquals(0, $teamX->trainees()->count());
        $this->assertEquals(1, $differentTeam->trainees()->count());
    }
}
