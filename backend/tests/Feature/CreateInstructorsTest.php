<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Mail\InstructorWelcomeEmail;
use App\Models\Back\Course;
use App\Models\Back\Instructor;
use App\Models\City;
use App\Models\User;
use App\Notifications\InstructorWelcomeNotification;
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
}
