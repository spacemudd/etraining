<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class UkCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $pdfContent;
    protected $filename;

    public function __construct($pdfContent = null, $filename = null)
    {
        $this->pdfContent = $pdfContent;
        $this->filename = $filename;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'شهادة تدريبية',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.uk-certificate',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->pdfContent && $this->filename) {
            $attachments[] = Attachment::fromData(
                fn() => $this->pdfContent,
                $this->filename
            )->withMime('application/pdf');
        }
        
        return $attachments;
    }
}
