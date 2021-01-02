<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Mail\InstructorWelcomeEmail;
use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use App\Models\City;
use App\Models\User;
use App\Notifications\InstructorWelcomeNotification;
use App\Notifications\TraineeGroupAnnouncementNotification;
use App\Services\RolesService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateInstructorsTest extends TestCase
{
    public function make_instructor()
    {
        // Make a new team.
        $admin = User::factory()->create();
        $team = $admin->ownedTeams()->create([
            'name' => 'eTraining Shafiq',
            'personal_team' => false,
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        return [
            'name' => 'Shafiq al-Shaar',
            'email' => 'shafiqalshaar@gmail.com',
            'identity_number' => '2020202010',
            'password' => 'password',
            'birthday' => '1994-12-10',
            'phone' => '966565176235',
            'phone_additional' => '966565176235',
            'city_id' => City::create(['name' => 'Riyadh'])->id,
            'twitter_link' => 'https://twitter.com/shafiqalshaar',
            'provided_courses' => 'pmp',
        ];
    }

    public function test_instructors_can_see_signup_page()
    {
        $this->get(route('register.instructors'))
            ->assertSuccessful();
    }

    public function test_instructors_register()
    {
        $instructor = $this->make_instructor();

        $this->post(route('register.instructors'), $instructor)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => $instructor['email'],
            'email_verified_at' => null,
        ]);

        $this->assertDatabaseHas('instructors', [
            'email' => $instructor['email'],
        ]);
    }

    public function test_instructor_redirected_to_completing_their_application()
    {
        $instructor_input = $this->make_instructor();

        $this->post(route('register.instructors'), $instructor_input);

        $instructor_profile = Instructor::whereEmail($instructor_input['email'])->first();
        $instructor_user = $instructor_profile->user;
        $this->actingAs($instructor_user)
            ->get(route('register.instructors.application'))
            ->assertSuccessful()
            ->assertPropValue('instructor_id', function ($instructor_id) use ($instructor_profile) {
                $this->assertEquals($instructor_id, $instructor_profile->id);
            })->assertPropValue('instructor_email', function ($instructor_email) use ($instructor_profile) {
                $this->assertEquals($instructor_email, $instructor_profile->email);
            });
    }

    public function test_instructor_files_are_retrieved_when_returning_to_the_application_at_a_later_time()
    {
        Storage::fake('s3');

        // Make a new team.
        $admin = User::factory()->create();
        $team = (new CreateTeam())->create($admin, [
            'name' => 'eTraining Shafiq',
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        $instructor_input = [
            'name' => 'Shafiq al-Shaar',
            'email' => 'shafiqalshaar@gmail.com',
            'identity_number' => '2020202010',
            'password' => 'password',
            'birthday' => '1994-12-10',
            'phone' => '966565176235',
            'phone_additional' => '966565176235',
            'city_id' => City::create(['name' => 'Riyadh'])->id,
            'twitter_link' => 'https://twitter.com/shafiqalshaar',
            'provided_courses' => 'pmp',
        ];

        $this->post(route('register.instructors'), $instructor_input);

        $allan = User::whereEmail($instructor_input['email'])->first();

        // Upload the CV.
        $this->actingAs($allan)
            ->post(route('api.register.instructors.upload-cv'), [
                'cv_summary' => UploadedFile::fake()->create('cv-summary-copy.jpg', 1024 * 24),
                'cv_full' => UploadedFile::fake()->create('cv-full-copy.jpg', 1024 * 24),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('media', [
            'model_id' => optional($allan->instructor)->id,
            'file_name' => 'cv-summary-copy.jpg',
        ]);

        $this->assertDatabaseHas('media', [
            'model_id' => optional($allan->instructor)->id,
            'file_name' => 'cv-full-copy.jpg',
        ]);

        $this->assertDatabaseHas('instructors', [
            'id' => $allan->instructor->id,
            'status' => Instructor::STATUS_PENDING_APPROVAL,
        ]);

        // todo: api call to get the media files.
    }

    public function test_redirecting_instructor_to_application_page_if_they_are_not_approved_yet()
    {
        // Make a new team.
        $admin = User::factory()->create();
        $team = (new CreateTeam())->create($admin, [
            'name' => 'eTraining Shafiq',
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        $instructor_input = [
            'name' => 'Shafiq al-Shaar',
            'email' => 'shafiqalshaar@gmail.com',
            'identity_number' => '2020202010',
            'password' => 'password',
            'birthday' => '1994-12-10',
            'phone' => '966565176235',
            'phone_additional' => '966565176235',
            'city_id' => City::create(['name' => 'Riyadh'])->id,
            'twitter_link' => 'https://twitter.com/shafiqalshaar',
            'provided_courses' => 'pmp',
        ];

        $this->post(route('register.instructors'), $instructor_input);

        $allan = User::whereEmail($instructor_input['email'])->first();

        $this->actingAs($allan)->get('/dashboard')
            ->assertRedirect(route('register.instructors.application'));
    }

    public function test_sending_welcome_email_to_instructors()
    {
        Notification::fake();

        // Make a new team.
        $admin = User::factory()->create();
        $team = (new CreateTeam())->create($admin, [
            'name' => 'eTraining Shafiq',
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        $instructor_input = [
            'name' => 'Shafiq al-Shaar',
            'email' => 'shafiqalshaar@gmail.com',
            'identity_number' => '2020202010',
            'password' => 'password',
            'birthday' => '1994-12-10',
            'phone' => '966565176235',
            'phone_additional' => '966565176235',
            'city_id' => City::create(['name' => 'Riyadh'])->id,
            'twitter_link' => 'https://twitter.com/shafiqalshaar',
            'provided_courses' => 'pmp',
        ];

        $this->post(route('register.instructors'), $instructor_input);
        $allan = User::whereEmail($instructor_input['email'])->first();
        // Upload the CV.
        $this->actingAs($allan)
            ->post(route('api.register.instructors.upload-cv'), [
                'cv_summary' => UploadedFile::fake()->create('cv-summary-copy.jpg', 1024 * 24),
                'cv_full' => UploadedFile::fake()->create('cv-full-copy.jpg', 1024 * 24),
            ])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($allan, InstructorWelcomeNotification::class);
    }

    public function test_instructor_can_submit_a_program_to_be_approved_by_the_administration()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $shafiqUser = User::factory()->create([
            'email' => 'shafiqalshaar@gmail.com',
        ]);

        $team = $admin->currentTeam;
        (new AddTeamMember())->add($shafiqUser, $team, $shafiqUser->email, 'instructor');

        $shafiqUser->current_team_id = $team->id;
        $shafiqUser->save();

        $shafiqProfile = Instructor::factory()->create([
            'user_id' => $shafiqUser->id,
            'email' => $shafiqUser->email,
            'status' => Instructor::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_id' => $admin->id,
        ]);

        $awsCourse = Course::factory()->make([
            'name_en' => 'AWS Course',
            'name_ar' => 'AWS سيرفر',
            'classroom_count' => 25,
            'description' => 'AWS courses for servers',
            'approval_code' => '10X',
            'days_duration' => 5,
            'hours_duration' => 30,
        ]);

        $this->actingAs($shafiqUser)
            ->post(route('teaching.courses.store'), $awsCourse->toArray())
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('courses', [
            'team_id' => $shafiqUser->current_team_id,
            'instructor_id' => $shafiqProfile->id,
            'name_en' => $awsCourse->name_en,
            'status' => Course::STATUS_PENDING,
        ]);
    }

    public function test_instructor_can_view_training_groups_assigned_to_them()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        // Instructor.
        $shafiqUser = User::factory()->create([
            'email' => 'shafiqalshaar@gmail.com',
        ]);
        $team = $admin->currentTeam;
        (new AddTeamMember())->add($shafiqUser, $team, $shafiqUser->email, 'instructor');
        $shafiqUser->current_team_id = $team->id;
        $shafiqUser->save();

        $shafiqProfile = Instructor::factory()->create([
            'user_id' => $shafiqUser->id,
            'email' => $shafiqUser->email,
            'status' => Instructor::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_id' => $admin->id,
        ]);

        // Instructor (X).
        $megamindUser = User::factory()->create([
            'email' => 'megamind@gmail.com',
        ]);
        (new AddTeamMember())->add($megamindUser, $team, $megamindUser->email, 'instructor');
        $megamindUser->current_team_id = $team->id;
        $megamindUser->save();

        $megamindUserProfile = Instructor::factory()->create([
            'user_id' => $megamindUser->id,
            'email' => $megamindUser->email,
            'status' => Instructor::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_id' => $admin->id,
        ]);

        // Trainee (1).
        $majdaTrainee = User::factory()->create([
            'email' => 'majda@gmail.com',
        ]);
        (new AddTeamMember())->add($majdaTrainee, $team, $majdaTrainee->email, 'trainee');
        $majdaTrainee->current_team_id = $team->id;
        $majdaTrainee->save();
        $majdaTraineeProfile = Trainee::factory()->create([
            'team_id' => $majdaTrainee->current_team_id,
            'user_id' => $majdaTrainee->id,
            'email' => $majdaTrainee->email,
        ]);

        // Trainee (2).
        $mikeTrainee = User::factory()->create([
            'email' => 'mike@gmail.com',
        ]);
        (new AddTeamMember())->add($mikeTrainee, $team, $mikeTrainee->email, 'trainee');
        $mikeTrainee->current_team_id = $team->id;
        $mikeTrainee->save();
        $mikeTraineeProfile = Trainee::factory()->create([
            'team_id' => $team->id,
            'user_id' => $mikeTrainee->id,
            'email' => $mikeTrainee->email,
        ]);

        // Trainee (3) / Not visible for the instructor.
        $sarahTraineeUser = User::factory()->create([
            'email' => 'sarah@gmail.com',
        ]);
        (new AddTeamMember())->add($sarahTraineeUser, $team, $sarahTraineeUser->email, 'trainee');
        $sarahTraineeUser->current_team_id = $team->id;
        $sarahTraineeUser->save();
        $sarahTraineeUserProfile = Trainee::factory()->create([
            'team_id' => $team->id,
            'user_id' => $sarahTraineeUser->id,
            'email' => $sarahTraineeUser->email,
        ]);

        // Course.
        $awsCourse = Course::factory()->create([
            'team_id' => $shafiqUser->current_team_id,
            'instructor_id' => $shafiqProfile->id,
            'name_en' => 'AWS Course',
            'name_ar' => 'AWS سيرفر',
            'classroom_count' => 25,
            'description' => 'AWS courses for servers',
            'approval_code' => '10X',
            'days_duration' => 5,
            'hours_duration' => 30,
        ]);

        // Assign trainees to a group.
        $acmeCompany = Company::factory()->create(['team_id' => $team->id]);
        $trainingGroup = TraineeGroup::factory()->create([
            'team_id' => $team->id,
            'company_id' => $acmeCompany->id,
            'name' => 'Golden 1',
        ]);
        $trainingGroup->trainees()->attach([$majdaTraineeProfile->id]);
        $trainingGroup_2 = TraineeGroup::factory()->create([
            'team_id' => $team->id,
            'company_id' => $acmeCompany->id,
            'name' => 'Golden 2',
        ]);
        $trainingGroup_2->trainees()->attach([$mikeTraineeProfile->id]);

        // A group that shouldn't be visible to the instructor
        $trainingGroupNotVisible = TraineeGroup::factory()->create([
            'team_id' => $team->id,
            'company_id' => $acmeCompany->id,
            'name' => 'Silver Not Visible',
        ]);
        $trainingGroupNotVisible->trainees()->attach([$sarahTraineeUserProfile->id]);

        // Assign trainees to the instructor
        $majdaTraineeProfile->instructor_id = $shafiqProfile->id;
        $majdaTraineeProfile->save();
        $mikeTraineeProfile->instructor_id = $shafiqProfile->id;
        $mikeTraineeProfile->save();

        $sarahTraineeUserProfile->instructor_id = $megamindUserProfile->id;
        $sarahTraineeUserProfile->save();

        $this->actingAs($shafiqUser)
            ->get(route('teaching.trainee-groups.index'))
            ->assertSuccessful()
            ->assertPropValue('traineeGroups', function($traineeGroups) use ($trainingGroup, $trainingGroup_2, $trainingGroupNotVisible) {
                $this->assertStringContainsString($trainingGroup->name, json_encode($traineeGroups));
                $this->assertStringContainsString($trainingGroup_2->name, json_encode($traineeGroups));
                $this->assertStringNotContainsString($trainingGroupNotVisible->name, json_encode($traineeGroups));
            });
    }

    public function test_viewing_form_to_send_an_announcement_to_training_groups()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        // Instructor.
        $shafiqUser = User::factory()->create([
            'email' => 'shafiqalshaar@gmail.com',
        ]);
        $team = $admin->currentTeam;
        (new AddTeamMember())->add($shafiqUser, $team, $shafiqUser->email, 'instructor');
        $shafiqUser->current_team_id = $team->id;
        $shafiqUser->save();

        $shafiqProfile = Instructor::factory()->create([
            'user_id' => $shafiqUser->id,
            'email' => $shafiqUser->email,
            'status' => Instructor::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_id' => $admin->id,
        ]);

        // Trainee (1).
        $majdaTrainee = User::factory()->create([
            'email' => 'majda@gmail.com',
        ]);
        (new AddTeamMember())->add($majdaTrainee, $team, $majdaTrainee->email, 'trainee');
        $majdaTrainee->current_team_id = $team->id;
        $majdaTrainee->save();
        $majdaTraineeProfile = Trainee::factory()->create([
            'team_id' => $majdaTrainee->current_team_id,
            'user_id' => $majdaTrainee->id,
            'email' => $majdaTrainee->email,
        ]);

        // Course.
        $awsCourse = Course::factory()->create([
            'team_id' => $shafiqUser->current_team_id,
            'instructor_id' => $shafiqProfile->id,
            'name_en' => 'AWS Course',
            'name_ar' => 'AWS سيرفر',
            'classroom_count' => 25,
            'description' => 'AWS courses for servers',
            'approval_code' => '10X',
            'days_duration' => 5,
            'hours_duration' => 30,
        ]);

        // Assign trainees to a group.
        $acmeCompany = Company::factory()->create(['team_id' => $team->id]);
        $trainingGroup = TraineeGroup::factory()->create([
            'team_id' => $team->id,
            'company_id' => $acmeCompany->id,
            'name' => 'Golden 1',
        ]);
        $trainingGroup->trainees()->attach([$majdaTraineeProfile->id]);

        // Assign trainees to the instructor
        $majdaTraineeProfile->instructor_id = $shafiqProfile->id;
        $majdaTraineeProfile->save();

        $this->actingAs($shafiqUser)
            ->get(route('teaching.trainee-groups.announcements.create', ['trainee_group_id' => $trainingGroup->id]))
            ->assertSuccessful();
    }

    public function test_sending_an_announcement_to_trainees_for_a_group()
    {
        Notification::fake();

        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        // Instructor.
        $shafiqUser = User::factory()->create([
            'email' => 'shafiqalshaar@gmail.com',
        ]);
        $team = $admin->currentTeam;
        (new AddTeamMember())->add($shafiqUser, $team, $shafiqUser->email, 'instructor');
        $shafiqUser->current_team_id = $team->id;
        $shafiqUser->save();

        $shafiqProfile = Instructor::factory()->create([
            'user_id' => $shafiqUser->id,
            'email' => $shafiqUser->email,
            'status' => Instructor::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_id' => $admin->id,
        ]);

        // Trainee (1).
        $majdaTrainee = User::factory()->create([
            'email' => 'majda@gmail.com',
        ]);
        (new AddTeamMember())->add($majdaTrainee, $team, $majdaTrainee->email, 'trainee');
        $majdaTrainee->current_team_id = $team->id;
        $majdaTrainee->save();
        $majdaTraineeProfile = Trainee::factory()->create([
            'team_id' => $majdaTrainee->current_team_id,
            'user_id' => $majdaTrainee->id,
            'email' => $majdaTrainee->email,
        ]);

        // Course.
        $awsCourse = Course::factory()->create([
            'team_id' => $shafiqUser->current_team_id,
            'instructor_id' => $shafiqProfile->id,
            'name_en' => 'AWS Course',
            'name_ar' => 'AWS سيرفر',
            'classroom_count' => 25,
            'description' => 'AWS courses for servers',
            'approval_code' => '10X',
            'days_duration' => 5,
            'hours_duration' => 30,
        ]);

        // Assign trainees to a group.
        $acmeCompany = Company::factory()->create(['team_id' => $team->id]);
        $trainingGroup = TraineeGroup::factory()->create([
            'team_id' => $team->id,
            'company_id' => $acmeCompany->id,
            'name' => 'Golden 1',
        ]);
        $trainingGroup->trainees()->attach([$majdaTraineeProfile->id]);

        // Assign trainees to the instructor
        $majdaTraineeProfile->instructor_id = $shafiqProfile->id;
        $majdaTraineeProfile->save();

        $announcement = [
            'course_id' => $awsCourse->id,
            'message' => 'Hello there!',
        ];

        $this->actingAs($shafiqUser)
            ->post(route('teaching.trainee-groups.announcements.send', ['trainee_group_id' => $trainingGroup->id]), $announcement)
            ->assertRedirect(route('teaching.trainee-groups.index'));

        Notification::assertSentTo($majdaTrainee, TraineeGroupAnnouncementNotification::class);
    }

    public function test_instructors_listing_all_trainees()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        // Instructor.
        $shafiqUser = User::factory()->create([
            'email' => 'shafiqalshaar@gmail.com',
        ]);
        $team = $admin->currentTeam;
        (new AddTeamMember())->add($shafiqUser, $team, $shafiqUser->email, 'instructor');
        $shafiqUser->current_team_id = $team->id;
        $shafiqUser->save();

        $shafiqProfile = Instructor::factory()->create([
            'user_id' => $shafiqUser->id,
            'email' => $shafiqUser->email,
            'status' => Instructor::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_id' => $admin->id,
        ]);

        // Trainee (1).
        $majdaTrainee = User::factory()->create([
            'email' => 'majda@gmail.com',
        ]);
        (new AddTeamMember())->add($majdaTrainee, $team, $majdaTrainee->email, 'trainee');
        $majdaTrainee->current_team_id = $team->id;
        $majdaTrainee->save();
        $majdaTraineeProfile = Trainee::factory()->create([
            'team_id' => $majdaTrainee->current_team_id,
            'user_id' => $majdaTrainee->id,
            'email' => $majdaTrainee->email,
        ]);

        // Course.
        $awsCourse = Course::factory()->create([
            'team_id' => $shafiqUser->current_team_id,
            'instructor_id' => $shafiqProfile->id,
            'name_en' => 'AWS Course',
            'name_ar' => 'AWS سيرفر',
            'classroom_count' => 25,
            'description' => 'AWS courses for servers',
            'approval_code' => '10X',
            'days_duration' => 5,
            'hours_duration' => 30,
        ]);

        // Assign trainees to a group.
        $acmeCompany = Company::factory()->create(['team_id' => $team->id]);
        $trainingGroup = TraineeGroup::factory()->create([
            'team_id' => $team->id,
            'company_id' => $acmeCompany->id,
            'name' => 'Golden 1',
        ]);
        $trainingGroup->trainees()->attach([$majdaTraineeProfile->id]);

        // Assign trainees to the instructor
        $majdaTraineeProfile->instructor_id = $shafiqProfile->id;
        $majdaTraineeProfile->save();

        $this->actingAs($shafiqUser)
            ->post(route('teaching.trainee-groups.trainees.index', ['trainee_group_id' => $trainingGroup->id]))
            ->assertPropValue('trainees', function ($trainees) use ($majdaTraineeProfile) {
               $this->assertEquals($trainees[0]['email'], $majdaTraineeProfile->email);
            });
    }
}
