<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use App\Models\Back\Course;
use App\Models\Back\Instructor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateCoursesManagementTest extends TestCase
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

    public function test_admin_can_see_all_courses_available()
    {
        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $pmpCourse = Course::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
            'name_en' => 'PMP Course',
            'classroom_count' => 25,
        ]);

        $this->actingAs($this->user)
            ->get(route('back.courses.index'))
            ->assertPropValue('courses', function ($courses) use ($pmpCourse) {
                $this->assertContains($pmpCourse->name, $courses['data'][0]);
            });
    }

    public function test_admin_can_save_new_course()
    {
        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $pmpCourse = Course::factory()->make([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
            'name_en' => 'PMP Course',
            'classroom_count' => 25,
        ])->toArray();

        $this->actingAs($this->user)
            ->post(route('back.courses.store'), $pmpCourse)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('courses', [
            'name_ar' => $pmpCourse['name_ar'],
        ]);
    }

    public function test_admin_viewing_course()
    {
        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $pmpCourse = Course::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
            'name_en' => 'PMP Course',
            'classroom_count' => 25,
        ])->toArray();

        $this->actingAs($this->user)
            ->get(route('back.courses.show', $pmpCourse['id']))
            ->assertPropValue('course', function($course) use ($pmpCourse, $instructor) {
                $this->assertEquals($course['instructor']['name'], $instructor->name);
                $this->assertEquals($course['name_en'], $pmpCourse['name_en']);
                $this->assertEquals($course['description'], $pmpCourse['description']);
            });
    }

    public function test_uploading_training_package_file_on_create()
    {
        Storage::fake('s3');

        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $pmpCourse = Course::factory()->make([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
            'name_en' => 'PMP Course',
            'classroom_count' => 25,
        ])->toArray();

        $pmpCourse['training_package'] = UploadedFile::fake()->create('training-package.jpg', 1024 * 24);

        $this->actingAs($this->user)
            ->post(route('back.courses.store'), $pmpCourse)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('media', [
            'model_id' => Course::where('name_en', $pmpCourse['name_en'])->first()->id,
            'file_name' => 'training-package.jpg',
        ]);
    }

    public function test_uploading_and_deleting_training_package_file_after_creating_course()
    {
        Storage::fake('s3');

        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $pmpCourse = Course::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
            'name_en' => 'PMP Course',
            'classroom_count' => 25,
        ]);

        $file = ['training_package' => UploadedFile::fake()->create('training-package.pdf', 1024 * 24)];

        $this->actingAs($this->user)
            ->post(route('back.courses.training-package', ['course_id' => $pmpCourse->id]), $file)
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('media', [
            'model_id' => $pmpCourse->id,
            'file_name' => 'training-package.pdf',
        ]);

        $this->actingAs($this->user)
            ->delete(route('back.courses.training-package.destroy', ['course_id' => $pmpCourse->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('media', [
            'model_id' => $pmpCourse->id,
            'file_name' => 'training-package.pdf',
        ]);
    }
}
