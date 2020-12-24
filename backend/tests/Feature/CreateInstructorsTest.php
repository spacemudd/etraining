<?php

namespace Tests\Feature;

use App\Actions\Jetstream\CreateTeam;
use App\Models\Back\Instructor;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
use App\Models\Team;
use App\Models\User;
use App\Services\RolesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
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
    }
}
