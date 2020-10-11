<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Instructor;
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
                'file' => UploadedFile::fake()->create('cv-full-copy.jpg', 1024 * 24),
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
                'file' => UploadedFile::fake()->create('cv-summary-copy.jpg', 1024 * 24),
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
}
