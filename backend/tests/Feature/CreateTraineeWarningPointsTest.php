<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CreateTraineeWarningPointsTest extends TestCase
{
    use WithFaker;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);
    }

    public function test_trainee_has_absent_points()
    {
        $joeDoe = Trainee::factory()->create([
            'team_id' => $this->admin->personalTeam()->id,
        ]);

        $this->assertEmpty($joeDoe->absents()->get());
    }

    public function test_trainee_gets_an_absent_record_when_missing_a_course()
    {
        $instructor = Instructor::factory([
            'team_id' => $this->admin->personalTeam()->id,
        ])->create();

        $company = Company::factory([
            'team_id' => $this->admin->personalTeam()->id,
        ])->create();

        $traineeGroup = TraineeGroup::factory([
            'team_id' => $company->team_id,
            'company_id' => $company->id,
        ])->create();

        $course = Course::factory([
            'team_id' => $company->team_id,
            'instructor_id' => $instructor->id,
        ])->create();
        $courseBatch = CourseBatch::factory([
            'team_id' => $company->team_id,
            'course_id' => $course->id,
            'trainee_group_id' => $traineeGroup->id,
        ])->create();
        $courseBatchSession = CourseBatchSession::factory([
            'team_id' => $company->team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->subHour(3),
            'ends_at' => now()->subMinute(10)
        ])->create();


        $joeDoe = Trainee::factory()->create([
            'team_id' => $company->team_id,
            'trainee_group_id' => $traineeGroup->id,
            'instructor_id' => $instructor->id,
        ]);

        // Instructor to submit attendance sheet

        $this->assertDatabaseHas('trainee_absents', [
            'trainee_id' => $joeDoe->id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'course_batch_session_id' => $courseBatchSession->id,
        ]);

        $this->assertDatabaseHas('course_batch_sessions', [
            'id' => $courseBatchSession->id,
            'processed_absentees' => true,
        ]);
    }
}
