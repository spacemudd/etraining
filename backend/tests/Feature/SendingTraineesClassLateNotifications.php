<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\AddTeamMember;
use App\Jobs\SendLateClassNotificationsJob;
use App\Models\Back\Company;
use App\Models\Back\Course;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use App\Models\User;
use App\Notifications\TraineeLateToClassNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Notification;
use Tests\TestCase;

class SendingTraineesClassLateNotifications extends TestCase
{
    use WithFaker;

    public function test_sending_notification_to_trainees_who_were_late()
    {
        Notification::fake();

        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);
        $team = $admin->currentTeam;

        // Instructor.
        $shafiqUser = User::factory()->create([
            'email' => 'shafiqalshaar@gmail.com',
        ]);
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
        $majdaTraineeProfile->trainee_group_id = $trainingGroup->id;
        // Assign trainees to the instructor
        $majdaTraineeProfile->instructor_id = $shafiqProfile->id;
        $majdaTraineeProfile->save();

        $batch = CourseBatch::factory()->create([
            'team_id' => $team->id,
            'course_id' => $awsCourse->id,
            'trainee_group_id' => $trainingGroup->id,
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->addDays(2),
            'location_at' => 'online',
        ]);

        $session = CourseBatchSession::factory()->create([
            'team_id' => $team->id,
            'course_id' => $awsCourse->id,
            'course_batch_id' => $batch->id,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHour(),
        ]);

        $attendance = new CourseBatchSessionAttendance([
            'team_id' => $team->id,
            'course_batch_id' => $session->course_batch_id,
            'course_batch_session_id' => $session->id,
            'course_id' => $session->course_id,
            'trainee_id' => $majdaTraineeProfile->id,
            'trainee_user_id' => $majdaTraineeProfile->user->id,
            'session_starts_at' => $session->starts_at,
            'session_ends_at' => $session->ends_at,
            'attended_at' => now()->subHour()->addMinutes(17),
            'attended' => false,
        ]);
        $attendance->team_id = $team->id;
        $attendance->save();

        SendLateClassNotificationsJob::dispatch($session);

        Notification::assertSentTo($majdaTraineeProfile, TraineeLateToClassNotification::class);
    }
}
