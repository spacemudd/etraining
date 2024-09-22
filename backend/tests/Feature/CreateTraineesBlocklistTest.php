<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeBlockList;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
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

    public function test_blocking_suspended_trainee_from_registering()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'admin@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);
        $blockedTrainee = TraineeBlockList::factory()->create(['team_id' => $admin->personalTeam()->id]);

        $this->post(route('register.trainees.store'), [
            'name' => $blockedTrainee->name,
            'email' => $blockedTrainee->email,
            'identity_number' => $blockedTrainee->identity_number,
            'password' => 'password@123123',
            'password_confirmation' => 'password@123123',
            'birthday' => '10-12-1994',
            'phone' => $blockedTrainee->phone,
            'phone_additional' => $blockedTrainee->phone_additional,
            'educational_level_id' => optional(EducationalLevel::withoutGlobalScopes()->first())->id,
            'city_id' => optional(City::withoutGlobalScopes()->first())->id,
            'national_address' => 'Riyadh',
            'marital_status_id' => optional(MaritalStatus::withoutGlobalScopes()->first())->id,
            'children_count' => 1,
            ])->assertSessionHasErrors();
    }

    /**
     *
     */
    public function test_trainee_cant_access_website_if_user_is_found_in_the_suspended_list()
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
            'skip_uploading_id' => true,
        ]);
        $nancy = (new CreateNewTraineeUser())->create([
            'trainee_id' => $trainee->id,
            'name' => $trainee->name,
            'email' => $trainee->email,
            'phone' => $trainee->phone,
            'national_address' => $trainee->national_address,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $trainee2 = Trainee::factory()->create([
            'team_id' => $admin->personalTeam()->id,
            'company_id' => $acmeCompany->id,
            'skip_uploading_id' => true,
        ]);
        $john = (new CreateNewTraineeUser())->create([
            'trainee_id' => $trainee2->id,
            'name' => $trainee2->name,
            'email' => $trainee2->email,
            'phone' => $trainee2->phone,
            'national_address' => $trainee2->national_address,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        TraineeBlockList::factory()->create([
            'team_id' => $admin->personalTeam()->id,
            'name' => $trainee->name,
            'email' => $trainee->email,
            'identity_number' => $trainee->identity_number,
            'phone' => $trainee->phone,
            'phone_additional' => $trainee->phone_additional,
        ]);

        $this->actingAs($nancy)
            ->get(route('dashboard'))
            ->assertStatus(412);
    }

    public function test_trainee_can_access_website_if_user_not_found_in_the_suspended_list()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'admin@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $acmeCompany = Company::factory()->create(['team_id' => $admin->personalTeam()->id]);

        $trainee2 = Trainee::factory()->create([
            'team_id' => $admin->personalTeam()->id,
            'company_id' => $acmeCompany->id,
            'skip_uploading_id' => true,
        ]);
        $john = (new CreateNewTraineeUser())->create([
            'trainee_id' => $trainee2->id,
            'name' => $trainee2->name,
            'email' => $trainee2->email,
            'phone' => $trainee2->phone,
            'national_address' => $trainee2->national_address,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->actingAs($john)
            ->get(route('dashboard'))
            ->assertSuccessful();
    }

    public function test_user_can_edit_suspended_user()
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
            'skip_uploading_id' => true,
        ]);
        $blockList = TraineeBlockList::factory()->create([
            'team_id' => $admin->personalTeam()->id,
            'name' => $trainee->name,
            'email' => $trainee->email,
            'identity_number' => $trainee->identity_number,
            'phone' => $trainee->phone,
            'phone_additional' => $trainee->phone_additional,
        ]);

        $this->actingAs($admin)
            ->get(route('back.trainees.suspend.edit', ['trainee_block_list_id' => $blockList->id]))
            ->assertPropValue('traineeBlockList', function ($traineeBlockList) use ($blockList) {
                $this->assertEquals($blockList->id, $traineeBlockList['id']);
            });
    }
}
