<?php

namespace App\Mail;

use App\Models\Back\Company;
use App\Services\CompanyMigrationHelper;
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
        $center = Company::find($company_id)->center;
        CompanyMigrationHelper::setMailgunConfigBasedOnDomain($center->domain_name);
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
            ->view('emails.new-company', [
                'company_name' => $company->name_ar,
                'company_link' => $company->show_url,
            ]);
    }
}
