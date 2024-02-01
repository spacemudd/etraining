<?php

namespace App\Mail;

use App\Models\Back\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCompanyEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $company_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $company = Company::find($this->company_id);

        return $this->subject('🔗 شركة جديدة: '.$company->name_ar)
            ->bcc('billing@ptc-ksa.net')
            ->view('emails.new-company', [
                'company_name' => $company->name_ar,
                'company_link' => $company->show_url,
            ]);
    }
}
