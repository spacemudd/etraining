<?php

namespace App\Mail;

use App\Models\Back\Invoice;
use App\Services\CompanyMigrationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EditInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $t;
    public $email;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Back\Invoice $invoice
     * @param \App\Models\Back\Invoice $t
     * @param $email
     */
    public function __construct(Invoice $invoice, Invoice $t, $email)
    {
        $this->invoice = $invoice;
        $this->t = $t;
        $this->email = $email;

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
            ->subject('[مهم] تم تغيير مبلغ الفاتورة 🔴')
            ->view('emails.edit-invoice', [
                'invoice' => $this->invoice,
                'old_inv' => $this->t,
                'email' => $this->email,
            ]);
    }
}
