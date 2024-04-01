<?php

namespace App\Mail;

use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Services\CompanyMigrationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EditAmountMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;

        $center = $invoice->company->center;
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
            ->subject('تم تغيير مبلغ الفاتورة من قبل المتدربة')
            ->view('emails.edit-amount', [
                'invoices' => $this->invoice,
            ]);
    }
}
