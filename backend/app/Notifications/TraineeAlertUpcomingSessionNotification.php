<?php

namespace App\Notifications;

use App\Models\Back\CourseBatchSession;
use App\Models\Back\TraineeGroup;
use App\Models\Back\Course;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TraineeAlertUpcomingSessionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var CourseBatchSession
     */
    private $session;

    /**
     * Create a new notification instance.
     *
     * @param CourseBatchSession $session
     */
    public function __construct(CourseBatchSession $session)
    {
        $this->session = $session;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $date = $this->session->starts_at
            ->setTimezone($notifiable->timezone ?: 'Asia/Riyadh');

        return (new MailMessage)
            ->subject($date->format('d-m-Y H:i').' - '.__('words.email-course-notification-subject-tomorrow').' (' . $this->session->course->name_ar.')')
            ->line(trans('words.dear-trainee'))
            ->line(trans('words.email-course-notification-body-at'). ' - '.$date->format('H:i'))
            ->salutation(trans('words.with-regards'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
