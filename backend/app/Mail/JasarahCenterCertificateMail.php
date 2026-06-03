<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Markdown;
use Illuminate\Queue\SerializesModels;

class JasarahCenterCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $pdfContent;
    protected $filename;
    protected string $recipientName;
    protected $courseName;
    protected $rowId;

    public function __construct($pdfContent, $filename, string $recipientName, $courseName, $rowId = null)
    {
        $this->pdfContent = $pdfContent;
        $this->filename = $filename;
        $this->recipientName = $recipientName;
        $this->courseName = $courseName;
        $this->rowId = $rowId;
    }

    public function build()
    {
        $mail = $this->from('certificates@mg.noreplycenter.com')
            ->subject('Notice of Attendance')
            ->markdown('emails.jasarah-center-certificate', [
                'recipientName' => $this->recipientName,
                'courseName' => $this->courseName,
            ]);

        if ($this->rowId) {
            $mail->withSwiftMessage(function ($message) {
                $message->getHeaders()
                    ->addTextHeader('X-Mailgun-Variables', json_encode([
                        'jasarah_center_certificate_row_id' => $this->rowId,
                        'type' => 'jasarah_center_certificate',
                    ]));
            });
        }

        if ($this->pdfContent && $this->filename) {
            $mail->attachData($this->pdfContent, $this->filename, ['mime' => 'application/pdf']);
        }

        return $mail;
    }

    /**
     * Render markdown using LTR-only mail components (not locale-based RTL layout).
     */
    protected function buildMarkdownView(): array
    {
        $markdown = new Markdown(app(ViewFactory::class), [
            'theme' => config('mail.markdown.theme', 'default'),
            'paths' => [
                resource_path('views/vendor/mail-ltr'),
            ],
        ]);

        $data = $this->buildViewData();

        return [
            'html' => $markdown->render($this->markdown, $data),
            'text' => $markdown->renderText($this->markdown, $data),
        ];
    }
}
