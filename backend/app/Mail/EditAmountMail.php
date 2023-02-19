<?php

namespace App\Mail;

use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
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
            ->subject('تم تغيير مبلغ الفاتورة من قبل المتدربة')
            ->view('emails.edit-amount', [
                'invoices' => $this->invoice,
            ]);
    }
}
