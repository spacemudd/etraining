<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
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

    public function test_trainee_gets_an_absent_record_when_missing_a_course()
    {
        $team_id = $this->admin->personalTeam()->id;
        $instructor = Instructor::factory()->create([
            'team_id' => $this->admin->personalTeam()->id,
        ]);

        $company = Company::factory()->create([
            'team_id' => $this->admin->personalTeam()->id,
        ]);

        $traineeGroup = TraineeGroup::factory()->create([
            'team_id' => $company->team_id,
            'company_id' => $company->id,
        ]);

        $course = Course::factory()->create([
            'team_id' => $company->team_id,
            'instructor_id' => $instructor->id,
        ]);
        $courseBatch = CourseBatch::factory()->create([
            'team_id' => $company->team_id,
            'course_id' => $course->id,
            'trainee_group_id' => $traineeGroup->id,
        ]);
        $courseBatchSession = CourseBatchSession::factory()->create([
            'team_id' => $company->team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->subHour(3),
            'ends_at' => now()->subMinute(10)
        ]);

        $joeDoe = Trainee::factory()->create(
            [
                'team_id' => $company->team_id,
                'trainee_group_id' => $traineeGroup->id,
                'instructor_id' => $instructor->id,
                'skip_uploading_id' => true,
            ]
        );



        //$joeDoe->status = 'absent_forgiven';
        //$joeDoe->absence_reason = 'Sick';

        // Instructor/Admin to submit attendance sheet
        $this->actingAs($this->admin)
            ->post(route('teaching.course-batch-sessions.attendance.store'), [
               'course_batch_session_id' => $courseBatchSession->id,
               'trainees' => [
                   $joeDoe,
               ],
            ])->assertSessionDoesntHaveErrors();

        dd($joeDoe->id);

        $this->assertDatabaseHas('course_batch_session_attendances', [
            'trainee_id' => $joeDoe->id,
            'status' => CourseBatchSessionAttendance::STATUS_ABSENT_FORGIVEN,
            'absent_reason' => 'Sick',
        ]);
        //
        //$this->assertDatabaseHas('course_batch_sessions', [
        //    'id' => $courseBatchSession->id,
        //    'processed_absentees' => true,
        //]);
    }
}
