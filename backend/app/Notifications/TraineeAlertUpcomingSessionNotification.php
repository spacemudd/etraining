<?php

namespace App\Notifications;

use App\Models\Back\TraineeGroup;
use App\Models\Back\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TraineeAlertUpcomingSessionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TraineeGroup $traineeGroup, Course $course, string $date)
    {
        $this->traineeGroup = $traineeGroup;
        $this->course = $course;
        $this->$date = $date;
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
        return (new MailMessage)
                    ->subject($this->date. trans('words.email-course-notification-subject'). ' - ' . $this->course->name_ar)
                    ->line(trans('words.dear-trainee'))
                    ->line(trans('words.email-course-notification-body'))
                    ->action(trans('words.access-the-platform'), url('/'))
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
