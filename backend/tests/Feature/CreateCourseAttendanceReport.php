<?php

namespace Tests\Feature;

use App\Exports\CourseSessionsAttendanceSummarySheetExport;
use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use App\Reports\CourseAttendanceReportFactory;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class CreateCourseAttendanceReport extends TestCase
{
    public function test_reports_page()
    {
        $shafiq = $this->makeMeAnAdmin();

        $this->actingAs($shafiq)
            ->get(route('back.reports.index'))
            ->assertSuccessful();

        $shafiq->roles()->first()->revokePermissionTo('view-backoffice-reports');

        $this->actingAs($shafiq)
            ->get(route('back.reports.index'))
            ->assertForbidden();
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function test_course_attendance_report_factory_selects_correct_sessions_based_on_date()
    {
        $shafiq = $this->makeMeAnAdmin();
        $team_id = $shafiq->personalTeam()->id;
        $instructor = Instructor::factory()->create([
            'team_id' => $team_id,
        ]);
        $company = Company::factory()->create([
            'team_id' => $team_id,
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

        $firstWeekSession = CourseBatchSession::factory()->create([
            'team_id' => $team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->setDate(2020, 1, 1)->setTime(13, 0, 0),
            'ends_at' => now()->setDate(2020, 1, 1)->setTime(14, 0, 0),
        ]);
        $secondWeekSession = CourseBatchSession::factory()->create([
            'team_id' => $team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->setDate(2020, 1, 17)->setTime(13, 0, 0),
            'ends_at' => now()->setDate(2020, 1, 17)->setTime(14, 0, 0),
        ]);

        $sessionShouldntBeSelected = CourseBatchSession::factory()->create([
            'team_id' => $team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->setDate(2019, 1, 17)->setTime(13, 0, 0),
            'ends_at' => now()->setDate(2019, 1, 17)->setTime(14, 0, 0),
        ]);

        // Create report.
        $sessions = CourseAttendanceReportFactory::new()
            ->setCourseId($course->id)
            ->setCompanyId($company->id)
            ->setStartDate(now()->setDate(2020, 1, 1))
            ->setEndDate(now()->setDate(2021, 1, 1))
            ->getCourseSessions();

        $this->assertCount(2, $sessions);
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function test_course_attendance_report_factory_selects_correct_trainees()
    {
        $shafiq = $this->makeMeAnAdmin();
        $team_id = $shafiq->personalTeam()->id;
        $instructor = Instructor::factory()->create([
            'team_id' => $team_id,
        ]);
        $company = Company::factory()->create([
            'team_id' => $team_id,
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

        $firstWeekSession = CourseBatchSession::factory()->create([
            'team_id' => $team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->setDate(2020, 1, 1)->setTime(13, 0, 0),
            'ends_at' => now()->setDate(2020, 1, 1)->setTime(14, 0, 0),
        ]);
        $secondWeekSession = CourseBatchSession::factory()->create([
            'team_id' => $team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->setDate(2020, 1, 17)->setTime(13, 0, 0),
            'ends_at' => now()->setDate(2020, 1, 17)->setTime(14, 0, 0),
        ]);

        $sessionShouldntBeSelected = CourseBatchSession::factory()->create([
            'team_id' => $team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->setDate(2019, 1, 17)->setTime(13, 0, 0),
            'ends_at' => now()->setDate(2019, 1, 17)->setTime(14, 0, 0),
        ]);

        // Trainees.

        $traineeWhoAttendedAll = Trainee::factory()->create([
            'team_id' => $team_id,
            'trainee_group_id' => $traineeGroup->id,
            'instructor_id' => $instructor->id,
        ]);
        $traineeWhoAttendedFirstSessionOnly = Trainee::factory()->create([
            'team_id' => $team_id,
            'trainee_group_id' => $traineeGroup->id,
            'instructor_id' => $instructor->id,
        ]);

        // Create report.
        $factory = CourseAttendanceReportFactory::new()
            ->setCourseId($course->id)
            ->setCompanyId($company->id)
            ->setStartDate(now()->setDate(2020, 1, 1))
            ->setEndDate(now()->setDate(2021, 1, 1));

        $sessions = $factory->getCourseSessions();
        $this->assertCount(2, $sessions);
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function test_course_attendance_report_factory_create_excel_with_correct_sheets()
    {
        Excel::fake();

        $shafiq = $this->makeMeAnAdmin();
        $team_id = $shafiq->personalTeam()->id;
        $instructor = Instructor::factory()->create([
            'team_id' => $team_id,
        ]);
        $company = Company::factory()->create([
            'team_id' => $team_id,
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

        $firstWeekSession = CourseBatchSession::factory()->create([
            'team_id' => $team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->setDate(2020, 1, 1)->setTime(13, 0, 0),
            'ends_at' => now()->setDate(2020, 1, 1)->setTime(14, 0, 0),
        ]);
        $secondWeekSession = CourseBatchSession::factory()->create([
            'team_id' => $team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->setDate(2020, 1, 17)->setTime(13, 0, 0),
            'ends_at' => now()->setDate(2020, 1, 17)->setTime(14, 0, 0),
        ]);

        $sessionShouldntBeSelected = CourseBatchSession::factory()->create([
            'team_id' => $team_id,
            'course_id' => $course->id,
            'course_batch_id' => $courseBatch->id,
            'starts_at' => now()->setDate(2019, 1, 17)->setTime(13, 0, 0),
            'ends_at' => now()->setDate(2019, 1, 17)->setTime(14, 0, 0),
        ]);

        // Trainees.

        $traineeWhoAttendedAll = Trainee::factory()->create([
            'team_id' => $team_id,
            'trainee_group_id' => $traineeGroup->id,
            'instructor_id' => $instructor->id,
        ]);
        $traineeWhoAttendedFirstSessionOnly = Trainee::factory()->create([
            'team_id' => $team_id,
            'trainee_group_id' => $traineeGroup->id,
            'instructor_id' => $instructor->id,
        ]);

        // Create report.
        $factory = CourseAttendanceReportFactory::new()
            ->setCourseId($course->id)
            ->setCompanyId($company->id)
            ->setStartDate(now()->setDate(2019, 12, 12))
            ->setEndDate(now()->setDate(2021, 1, 1))
            ->toExcel();

        Excel::assertStored(storage_path('/reports/2019-12-12-AttendanceReport-2021-01-01.xlsx'), 's3',
            function(CourseSessionsAttendanceSummarySheetExport $export) {
                return count($export->sheets()) === 3;
        });
    }
}
