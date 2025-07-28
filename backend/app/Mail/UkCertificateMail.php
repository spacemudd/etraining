<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UkCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $pdfContent;
    protected $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdfContent = null, $filename = null)
    {
        $this->pdfContent = $pdfContent;
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject('شهادة تدريبية')
                     ->markdown('emails.uk-certificate');
        
        if ($this->pdfContent && $this->filename) {
            $mail->attachData($this->pdfContent, $this->filename, ['mime' => 'application/pdf']);
        }
        
        return $mail;
    }
}
