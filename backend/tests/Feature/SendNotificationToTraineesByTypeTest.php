<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use App\Notifications\CustomTraineeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendNotificationToTraineesByTypeTest extends TestCase
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

    public function test_admin_can_send_notification_to_candidates()
    {
        Notification::fake();

        $acmeCompany = Company::factory()->create(['team_id' => $this->user->personalTeam()->id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'company_id' => $acmeCompany->id,
            'status' => Trainee::STATUS_PENDING_APPROVAL,
        ]);

        $msg = [
            'to_trainees_status' => Trainee::STATUS_PENDING_APPROVAL,
            'email_title' => 'Notice email',
            'email_body' =>  'This is a notice email',
            'sms_body' => 'This is a notice email',
        ];

        $this->actingAs($this->user)
            ->post(route('back.trainees.send-notification.send'), $msg);

        Notification::assertSentTo($trainee, function(CustomTraineeNotification $notification) use ($msg) {
            return (
                $notification->emailTitle === $msg['email_title']
                &&
                $notification->emailBody === $msg['email_body']
                &&
                $notification->smsBody === $msg['sms_body']
            );
        });
    }

    public function test_admin_can_send_notification_to_candidates_of_today_only()
    {
        Notification::fake();

        $acmeCompany = Company::factory()->create(['team_id' => $this->user->personalTeam()->id]);
        $trainee = Trainee::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'company_id' => $acmeCompany->id,
            'status' => Trainee::STATUS_PENDING_APPROVAL,
        ]);

        $traineeIgnored = Trainee::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'company_id' => $acmeCompany->id,
            'status' => Trainee::STATUS_PENDING_APPROVAL,
            'created_at' => now()->subDay(),
        ]);

        $msg = [
            'to_trainees_status' => Trainee::STATUS_PENDING_APPROVAL,
            'registered_today_only' => true,
            'email_title' => 'Notice email',
            'email_body' =>  'This is a notice email',
            'sms_body' => 'This is a notice email',
        ];

        $this->actingAs($this->user)
            ->post(route('back.trainees.send-notification.send'), $msg);

        Notification::assertSentTo($trainee, function(CustomTraineeNotification $notification) use ($msg) {
            return (
                $notification->emailTitle === $msg['email_title']
                &&
                $notification->emailBody === $msg['email_body']
                &&
                $notification->smsBody === $msg['sms_body']
            );
        });

        Notification::assertNotSentTo($traineeIgnored, CustomTraineeNotification::class);
    }
}
