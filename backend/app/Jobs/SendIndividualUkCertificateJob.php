<?php

namespace App\Jobs;

use App\Models\Back\UkCertificateRow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendIndividualUkCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 1;

    protected $rowId;

    public function __construct($rowId)
    {
        $this->rowId = $rowId;
    }

    public function handle()
    {
        $row = UkCertificateRow::find($this->rowId);
        
        if (!$row || $row->status !== 'pending' || !$row->pdf_path) {
            return;
        }

        try {
            $pdfContent = Storage::disk('s3')->get($row->pdf_path);
            
            if ($pdfContent) {
                // Send email with row ID for tracking
                $mail = Mail::to($row->trainee->email)
                    ->bcc(['shafiqalshaar@adv-line.com', 'mashael.a@hadaf-hq.com'])
                    ->send(new \App\Mail\UkCertificateMail($pdfContent, basename($row->pdf_path), $row->trainee, $this->rowId));
                
                // Extract Mailgun message ID from the sent message
                $messageId = null;
                if (method_exists($mail, 'getSymfonySentMessage')) {
                    $symfonyMessage = $mail->getSymfonySentMessage();
                    if ($symfonyMessage) {
                        $messageId = $symfonyMessage->getMessageId();
                    }
                }
                
                $row->update([
                    'sent_at' => now(),
                    'status' => 'sent',
                    'mailgun_message_id' => $messageId,
                    'delivery_status' => UkCertificateRow::DELIVERY_STATUS_PENDING,
                ]);
                
                // Check if all emails have been sent and update main certificate status
                $ukCertificate = $row->ukCertificate;
                if ($ukCertificate) {
                    $ukCertificate->checkAndUpdateCompletionStatus();
                }
            } else {
                throw new \Exception('PDF content is empty or could not be retrieved from S3');
            }
        } catch (\Exception $e) {
            $row->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'delivery_status' => UkCertificateRow::DELIVERY_STATUS_FAILED,
                'failed_at' => now(),
                'delivery_failure_reason' => $e->getMessage(),
            ]);
            \Log::error('Failed to send UK certificate for row ' . $this->rowId . ': ' . $e->getMessage());
        }
    }
} 