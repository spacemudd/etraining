<?php

namespace App\Notifications;

use App\Models\Back\Course;
use App\Models\Back\TraineeGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TraineeGroupAnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var \App\Models\Back\TraineeGroup
     */
    public $traineeGroup;

    /**
     * @var \App\Models\Back\Course
     */
    public $course;

    /**
     * @var string
     */
    public $message;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Back\TraineeGroup $traineeGroup
     * @param \App\Models\Back\Course $course
     * @param string $message
     */
    public function __construct(TraineeGroup $traineeGroup, Course $course, string $message)
    {
        $this->traineeGroup = $traineeGroup;
        $this->course = $course;
        $this->message = $message;
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
                    ->subject(trans('words.announcement-regarding-the-course'). ' - ' . $this->course->name_ar)
                    ->line(trans('words.announcement-regarding-the-course'). ' - ' . $this->course->name_ar)
                    ->line($this->message)
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
