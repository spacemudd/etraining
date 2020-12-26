<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use App\Models\Back\Instructor;
use App\Models\City;
use App\Models\User;
use App\Services\RolesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

class InstructorsManagementTest extends TestCase
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

    public function test_user_can_access_instructor_management_page()
    {
        $instructor = Instructor::factory()->create(['team_id' => $this->user->personalTeam()->id]);

        $this->actingAs($this->user)
            ->get(route('back.instructors.index'))
            ->assertSuccessful()
            ->assertPropValue('instructors', function ($instructors) use ($instructor) {
                $this->assertContains($instructor->name, $instructors['data'][0]);
            });
    }

    public function test_user_can_see_new_instructor_form()
    {
        $this->actingAs($this->user)
            ->get(route('back.instructors.create'))
            ->assertSuccessful();
    }

    public function test_user_can_create_a_instructor()
    {
        $instructor = [
            'name' => 'Shafiq al-Shaar',
            'identity_number' => '10000',
            'phone' => '96670000000',
            'email' => 'hello@getshafiq.com',
            'twitter_link' => 'https://twitter.com/getshafiq',
            'city_id' => null,
        ];

        $this->actingAs($this->user)
            ->post(route('back.instructors.store'), $instructor)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('instructors', $instructor);
    }

    public function test_uploading_and_deleting_cv_full_file()
    {
        Storage::fake('s3');

        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('back.instructors.attachments.cv-full', ['instructor_id' => $instructor->id]), [
                'cv_full' => UploadedFile::fake()->create('cv-full-copy.jpg', 1024 * 24),
            ])
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('media', [
            'model_id' => $instructor->id,
            'file_name' => 'cv-full-copy.jpg',
        ]);

        $this->actingAs($this->user)
            ->delete(route('back.instructors.attachments.cv-full.destroy', ['instructor_id' => $instructor->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('media', [
            'model_id' => $instructor->id,
            'file_name' => 'cv-full-copy.jpg',
        ]);
    }

    public function test_uploading_and_deleting_cv_summary_file()
    {
        Storage::fake('s3');

        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('back.instructors.attachments.cv-summary', ['instructor_id' => $instructor->id]), [
                'cv_summary' => UploadedFile::fake()->create('cv-summary-copy.jpg', 1024 * 24),
            ])
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('media', [
            'model_id' => $instructor->id,
            'file_name' => 'cv-summary-copy.jpg',
        ]);

        $this->actingAs($this->user)
            ->delete(route('back.instructors.attachments.cv-summary.destroy', ['instructor_id' => $instructor->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('media', [
            'model_id' => $instructor->id,
            'file_name' => 'cv-summary-copy.jpg',
        ]);
    }

    public function test_admin_creating_a_user_for_the_instructor()
    {
        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('back.instructors.create-user', ['instructor_id' => $instructor->id]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('back.instructors.show', $instructor->id));

        $this->assertDatabaseHas('users', [
            'email' => $instructor->email,
        ]);

        $this->assertDatabaseHas('instructors', [
            'email' => $instructor->email,
            'user_id' => User::findByEmail($instructor->email)->first()->id,
        ]);
    }

    public function test_linking_an_instructor_to_contract()
    {
        $company = Company::factory()->create(['team_id' => $this->user->personalTeam()->id]);
        $contract = CompanyContract::factory()->create(['team_id' => $this->user->personalTeam()->id, 'company_id' => $company->id]);
        $instructor = Instructor::factory()->create(['team_id' => $this->user->personalTeam()->id]);

        $this->actingAs($this->user)
            ->post(route('back.company-contracts.attach-instructor'), [
                'company_contract_id' => $contract->id,
                'instructor_id' => $instructor->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('company_contract_instructor', [
            'company_contract_id' => $contract->id,
            'instructor_id' => $instructor->id,
        ]);
    }

    public function test_unlinking_an_instructor_from_contract()
    {
        $company = Company::factory()->create(['team_id' => $this->user->personalTeam()->id]);
        $contract = CompanyContract::factory()->create(['team_id' => $this->user->personalTeam()->id, 'company_id' => $company->id]);
        $instructor = Instructor::factory()->create(['team_id' => $this->user->personalTeam()->id]);

        $contract->instructors()->attach([$instructor->id]);

        $this->actingAs($this->user)
            ->post(route('back.company-contracts.detach-instructor'), [
                'company_contract_id' => $contract->id,
                'instructor_id' => $instructor->id,
            ])
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseMissing('company_contract_instructor', [
            'company_contract_id' => $contract->id,
            'instructor_id' => $instructor->id,
        ]);
    }

    public function test_instructor_is_shown_as_pending_completing_application()
    {
        $admin = $this->user;

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
        $instructor = Instructor::whereEmail($instructor_input['email'])->first();

        $this->actingAs($admin)
            ->get(route('back.instructors.index'))
            ->assertSuccessful()
            ->assertPropValue('instructors', function ($instructors) use ($instructor) {
                $this->assertContains($instructor->email, $instructors['data'][0]);
                $this->assertTrue($instructors['data'][0]['is_pending_uploading_files']);
            });
    }

    public function test_instructor_marked_as_pending_approval_when_they_finish_uploading_their_cv()
    {
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
        $instructor = Instructor::whereEmail($instructor_input['email'])->first();
        $this->actingAs($instructor->user)
            ->post(route('api.register.instructors.upload-cv'), [
                'cv_summary' => UploadedFile::fake()->create('cv-summary-copy.jpg', 1024 * 24),
                'cv_full' => UploadedFile::fake()->create('cv-full-copy.jpg', 1024 * 24),
            ])->assertSuccessful();

        $this->assertDatabaseHas('instructors', [
            'id' => $instructor->id,
            'status' => Instructor::STATUS_PENDING_APPROVAL,
        ]);

        $instructor->refresh();
        $this->assertTrue($instructor->is_pending_approval);
    }

    public function test_approving_instructor_to_access_the_platform()
    {
        $admin = $this->user;

        $shafiqUser = User::factory()->create([
            'email' => 'shafiqalshaar@gmail.com',
        ]);

        $team = $this->user->currentTeam;
        (new AddTeamMember())->add($shafiqUser, $team, $shafiqUser->email, 'instructor');

        $shafiqUser->current_team_id = $team->id;
        $shafiqUser->save();

        $shafiqProfile = Instructor::factory()->create([
            'user_id' => $shafiqUser->id,
            'email' => $shafiqUser->email,
            'status' => Instructor::STATUS_PENDING_APPROVAL,
        ]);

        $this->actingAs($admin)
            ->post(route('back.instructors.approve-user', $shafiqProfile->id))
            ->assertRedirect(route('back.instructors.show', $shafiqProfile->id));

        $this->assertDatabaseHas('instructors', [
            'id' => $shafiqProfile->id,
            'status' => Instructor::STATUS_APPROVED,
            'approved_by_id' => $admin->id,
        ]);

        $this->assertDatabaseMissing('instructors', [
            'approved_at' => null,
        ]);
    }
}
