<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\AddTeamMember;
use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\Instructor;
use App\Models\Back\TraineeGroup;
use App\Models\User;
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
            'instructor_Id' => $instructor->id,
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

        $file = ['file' => UploadedFile::fake()->create('training-package.pdf', 1024 * 24)];

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

    public function test_admin_can_create_course_batch()
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

        $traineeGroup = TraineeGroup::factory()->create([
            'company_id' => Company::factory()->create(['team_id' => $this->user->personalTeam()->id]),
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $batch = [
            'trainee_group_id' => $traineeGroup->id,
            'course_id' => $pmpCourse->id,
            'starts_at' => now()->firstOfMonth(),
            'ends_at' => now()->endOfMonth(),
            'location_at' => 'online',
        ];

        $this->actingAs($this->user)
            ->post(route('back.course-batches.store', ['course_id' => $pmpCourse->id]), $batch)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('course_batches', $batch);
    }

    public function test_admin_can_see_all_course_batches()
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

        $batch = CourseBatch::factory()->create([
            'course_id' => $pmpCourse->id,
            'team_id' => $pmpCourse->team_id,
        ]);

        $batch_2 = CourseBatch::factory()->create([
            'course_id' => $pmpCourse->id,
            'team_id' => $pmpCourse->team_id,
        ]);

        $this->actingAs($this->user)
            ->get(route('back.course-batches.index', ['course_id' => $pmpCourse->id]))
            ->assertSeeText($batch->course_id)
            ->assertSeeText($batch->starts_at_timezone)
            ->assertSeeText($batch_2->course_id)
            ->assertSeeText($batch_2->ends_at_timezone);
    }

    public function test_admin_can_create_course_sessions()
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

        $batch = CourseBatch::factory()->create([
            'course_id' => $pmpCourse->id,
            'team_id' => $pmpCourse->team_id,
        ]);

        $this->actingAs($this->user)
            ->post(route('back.course-batch-sessions.store', [
                'course_id' => $pmpCourse->id,
                'course_batch_id' => $batch->id,
            ]), [
                'course_batch_id'  => $batch->id,
                'starts_at' => now()->setHour(11)->setMinute(0),
                'ends_at' => now()->setHour(12)->setMinute(0),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('course_batch_sessions', [
            'course_id' => $pmpCourse->id,
            'course_batch_id' => $batch->id,
            'starts_at' => now()->setHour(11)->setMinute(0)->toDateTimeString(),
            'ends_at' => now()->setHour(12)->setMinute(0)->toDateTimeString(),
        ]);
    }

    public function test_admin_can_see_course_sessions()
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

        $batch = CourseBatch::factory()->create([
            'course_id' => $pmpCourse->id,
            'team_id' => $pmpCourse->team_id,
        ]);

        $session = CourseBatchSession::factory()->create([
            'course_id' => $pmpCourse->id,
            'course_batch_id' => $batch->id,
            'team_id' => $pmpCourse->team_id,
        ]);

        $this->actingAs($this->user)
            ->get(route('back.course-batch-sessions.index', [
                'course_id' => $pmpCourse->id,
                'course_batch_id' => $batch->id,
            ]))
            ->assertSuccessful()
            ->assertSeeText($session->starts_at);
    }

    public function test_admin_approving_a_course_created_by_an_instructor()
    {
        $admin = $this->user;

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

        $awsCourse = Course::factory()->create([
            'team_id' => $shafiqUser->current_team_id,
            'instructor_id' => $shafiqProfile->id,
            'status' => Course::STATUS_PENDING,
            'name_en' => 'AWS Course',
            'name_ar' => 'AWS سيرفر',
            'classroom_count' => 25,
            'description' => 'AWS courses for servers',
            'approval_code' => '10X',
            'days_duration' => 5,
            'hours_duration' => 30,
        ]);

        $this->actingAs($admin)
            ->post(route('back.courses.approve', $awsCourse->id));

        $this->assertDatabaseHas('courses', [
            'team_id' => $shafiqUser->current_team_id,
            'instructor_id' => $shafiqProfile->id,
            'name_en' => $awsCourse->name_en,
            'status' => Course::STATUS_APPROVED,
        ]);
    }
}
