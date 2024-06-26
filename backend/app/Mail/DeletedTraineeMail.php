<?php

namespace App\Mail;

use App\Models\Back\Company;
use App\Models\Back\Trainee;
use App\Services\CompanyMigrationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeletedTraineeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $trainee;
    public $email;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Back\Trainee $trainee
     * @param $email
     */
    public function __construct(Trainee $trainee, $email)
    {
        $this->trainee = $trainee;
        $this->email = $email;

        $center = Company::withTrashed()->find($this->company_id)->center;
        CompanyMigrationHelper::setMailgunConfigBasedOnDomain($center->domain);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('حذف متدربة')
            ->view('emails.deleted-trainee', [
                'trainee' => $this->trainee,
                'email' => $this->email,
            ]);
    }
}
