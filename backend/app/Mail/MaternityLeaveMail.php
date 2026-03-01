<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeLeave;

class MaternityLeaveMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainee;
    public $leave;
    public $emailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Trainee $trainee, TraineeLeave $leave, array $emailData = [])
    {
        $this->trainee = $trainee;
        $this->leave = $leave;
        $this->emailData = $emailData;

        \Log::info('MaternityLeaveMail Constructor Called', [
            'trainee_id' => $trainee->id,
            'trainee_name' => $trainee->name,
            'leave_id' => $leave->id,
            'leave_type' => $leave->leave_type,
            'from_date' => $leave->from_date,
            'to_date' => $leave->to_date,
            'company_name' => $trainee->company ? $trainee->company->name_ar : 'No company'
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        \Log::info('MaternityLeaveMail Build Method Called', [
            'trainee_id' => $this->trainee->id,
            'trainee_name' => $this->trainee->name,
            'leave_id' => $this->leave->id
        ]);

        $companyNameAr = optional($this->trainee->company)->name_ar ?? $this->trainee->name;
        $subject = 'إشعار إجازة وضع - ' . $companyNameAr;
        
        \Log::info('Email Subject Prepared', [
            'subject' => $subject,
            'view' => 'emails.maternity-leave'
        ]);

        $mail = $this->subject($subject)
                    ->view('emails.maternity-leave');

        \Log::info('MaternityLeaveMail Build Completed Successfully');

        return $mail;
    }
}
