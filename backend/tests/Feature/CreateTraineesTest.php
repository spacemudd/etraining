<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\InboxMessage;
use App\Models\MaritalStatus;
use App\Models\User;
use App\Notifications\AssignedToCompanyTraineeNotification;
use App\Notifications\TraineeWelcomeNotification;
use App\Services\RolesService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateTraineesTest extends TestCase
{
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
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => $trainee['email'],
            'email_verified_at' => null,
        ]);

        $this->assertDatabaseHas('trainees', [
            'email' => $trainee['email'],
        ]);
    }

    public function test_trainee_redirected_to_completing_their_application()
    {
        $trainee_input = $this->make_trainee();

        $this->post(route('register.trainees'), $trainee_input);

        $trainee_profile = Trainee::whereEmail($trainee_input['email'])->first();
        $trainee_user = $trainee_profile->user;
        $this->actingAs($trainee_user)
            ->get(route('register.trainees.application'))
            ->assertSuccessful()
            ->assertPropValue('trainee_id', function ($trainee_id) use ($trainee_profile) {
                $this->assertEquals($trainee_id, $trainee_profile->id);
            })->assertPropValue('trainee_email', function ($trainee_email) use ($trainee_profile) {
                $this->assertEquals($trainee_email, $trainee_profile->email);
            });
    }

    public function test_trainees_files_are_retrieved_when_returning_to_the_application_at_a_later_time()
    {
        Storage::fake('s3');

        // Make a new team.
        $admin = User::factory()->create();
        $team = (new CreateTeam())->create($admin, [
            'name' => 'eTraining Shafiq',
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        $trainee_input = $this->make_trainee();

        $this->post(route('register.trainees'), $trainee_input);

        $allan = User::whereEmail($trainee_input['email'])->first();

        // Upload the CV.
        $this->actingAs($allan)
            ->post(route('api.register.trainees.upload-cv'), [
                'identity_card_copy' => UploadedFile::fake()->create('identity-card-copy.jpg', 1024 * 24),
                'qualification_copy' => UploadedFile::fake()->create('qualification-copy.jpg', 1024 * 24),
                'bank_account_copy' => UploadedFile::fake()->create('bank-account-copy.jpg', 1024 * 24),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('media', [
            'model_id' => optional($allan->trainee)->id,
            'file_name' => 'identity-card-copy.jpg',
        ]);

        $this->assertDatabaseHas('media', [
            'model_id' => optional($allan->trainee)->id,
            'file_name' => 'qualification-copy.jpg',
        ]);

        $this->assertDatabaseHas('media', [
            'model_id' => optional($allan->trainee)->id,
            'file_name' => 'bank-account-copy.jpg',
        ]);

        $this->assertDatabaseHas('trainees', [
            'id' => $allan->trainee->id,
            'status' => Trainee::STATUS_PENDING_APPROVAL,
        ]);

        // todo: api call to get the media files.
    }

    public function test_redirecting_trainee_to_application_page_if_they_are_not_approved_yet()
    {
        // Make a new team.
        $admin = User::factory()->create();
        $team = (new CreateTeam())->create($admin, [
            'name' => 'eTraining Shafiq',
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        $trainee_input = $this->make_trainee();

        $this->post(route('register.trainees'), $trainee_input);

        $allan = User::whereEmail($trainee_input['email'])->first();

        $this->actingAs($allan)->get('/dashboard')
            ->assertRedirect(route('register.trainees.application'));
    }

    public function test_trainees_can_see_courses_index_page()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $majda = User::factory()->create([
            'email' => 'majda@gmail.com',
        ]);

        $team = $admin->currentTeam;
        (new AddTeamMember())->add($majda, $team, $majda->email, 'trainee');

        $majda->current_team_id = $team->id;
        $majda->save();

        $majdaTraineeProfile = Trainee::factory()->create([
            'team_id' => $majda->current_team_id,
            'user_id' => $majda->id,
            'email' => $majda->email,
        ]);

        $this->actingAs($majda)
            ->get(route('trainees.courses.index'))
            ->assertSuccessful();
    }

    public function test_sending_welcome_email_to_trainees()
    {
        Notification::fake();

        // Make a new team.
        $admin = User::factory()->create();
        $team = (new CreateTeam())->create($admin, [
            'name' => 'eTraining Shafiq',
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        $trainee_input = $this->make_trainee();

        $this->post(route('register.trainees'), $trainee_input);
        $allan = User::whereEmail($trainee_input['email'])->first();
        // Upload the CV.
        $this->actingAs($allan)
            ->post(route('api.register.trainees.upload-cv'), [
                'identity_card_copy' => UploadedFile::fake()->create('identity-card-copy.jpg', 1024 * 24),
                'qualification_copy' => UploadedFile::fake()->create('qualification-copy.jpg', 1024 * 24),
                'bank_account_copy' => UploadedFile::fake()->create('bank-account-copy.jpg', 1024 * 24),
            ])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($allan, TraineeWelcomeNotification::class);
    }

    // TODO.
    //public function test_trainee_redirected_to_completing_their_application()
    //{
    //    // Make a new team.
    //    $admin = User::factory()->create();
    //    $team = $admin->ownedTeams()->create([
    //        'name' => 'eTraining Shafiq',
    //        'personal_team' => false,
    //    ]);
    //    app()->make(RolesService::class)->seedRolesToTeam($team);
    //
    //    $trainee = [
    //        'name' => 'Shafiq al-Shaar',
    //        'email' => 'shafiqalshaar@gmail.com',
    //        'identity_number' => '2020202010',
    //        'password' => 'password',
    //        'password_confirmation' => 'password',
    //        'birthday' => '1994-12-10',
    //        'phone' => '966565176235',
    //        'phone_additional' => '966565176235',
    //        'educational_level_id' => EducationalLevel::create(['order' => 1, 'name_en' => 'College_'])->id,
    //        'city_id' => City::create(['name' => 'Riyadh'])->id,
    //        'marital_status_id' => MaritalStatus::create(['order' => 1, 'name_en' => 'Single_'])->id,
    //        'children_count' => 0,
    //    ];
    //
    //    $this->post(route('register.trainees'), $trainee);
    //
    //    $this->actingAs(User::whereEmail($trainee['email'])->first())
    //        ->get(route('dashboard'))
    //        ->assertRedirect(route('trainees.application'));
    //}

    public function test_trainee_viewing_their_message()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $majda = User::factory()->create([
            'email' => 'majda@gmail.com',
        ]);

        $team = $admin->currentTeam;
        (new AddTeamMember())->add($majda, $team, $majda->email, 'trainee');
        $majda->current_team_id = $team->id;
        $majda->save();

        $majdaTraineeProfile = Trainee::factory()->create([
            'team_id' => $majda->current_team_id,
            'user_id' => $majda->id,
            'email' => $majda->email,
        ]);

        $message = InboxMessage::factory()->create([
            'team_id' => $majda->current_team_id,
            'to_id' => $majda->id,
        ]);

        $this->actingAs($majda)
            ->get(route('inbox.index'))
            ->assertSuccessful()
            ->assertPropValue('messages', function($messages) use ($message) {
                $this->assertEquals($message->body, $messages[0]['body']);
            });
    }

    public function test_unapproved_trainee_can_see_dashboard_if_skip_flag_is_enabled_for_them()
    {
        $trainee_input = $this->make_trainee();

        $this->post(route('register.trainees'), $trainee_input);

        $trainee_profile = Trainee::whereEmail($trainee_input['email'])->first();
        $trainee_profile->skip_uploading_id = true;
        $trainee_profile->save();

        $trainee_user = $trainee_profile->user;
        $this->actingAs($trainee_user)
            ->get(route('dashboard'))
            ->assertSuccessful();
    }

    public function test_trainee_receives_an_email_when_they_are_assigned_a_company()
    {
        Notification::fake();

        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'admin@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $acmeCompany = Company::factory()->create(['team_id' => $admin->personalTeam()->id]);
        $johnCompany = Company::factory()->create(['team_id' => $admin->personalTeam()->id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $admin->personalTeam()->id,
            'company_id' => $acmeCompany->id,
        ]);

        $this->actingAs($admin)->put(route('back.trainees.update', $trainee->id), [
            'company_id' => $johnCompany->id,
            'email' => $trainee->email,
            'name' => $trainee->name,
            'identity_number' => $trainee->identity_number,
            'phone' => $trainee->phone,
        ])->assertSessionDoesntHaveErrors();

        Notification::assertSentTo($trainee, AssignedToCompanyTraineeNotification::class);
    }
}
