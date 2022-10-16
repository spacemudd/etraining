<?php

namespace App\Mail;

use App\Models\Back\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EditInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Back\Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
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
            ]);
    }
}
