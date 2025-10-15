<?php

namespace App\Jobs;

use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;
    protected $mailClass;
    protected $logContext;
    protected $batchSize;

    public $tries = 3;
    public $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(array $emailData, $mailClass, string $logContext = 'Bulk Email', int $batchSize = 10)
    {
        $this->emailData = $emailData;
        $this->mailClass = $mailClass;
        $this->logContext = $logContext;
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting {$this->logContext} job", [
                'email_data' => $this->emailData,
                'batch_size' => $this->batchSize
            ]);

            // معالجة الايميلات
            $toEmails = EmailHelper::processEmails($this->emailData['to'] ?? '');
            $ccEmails = EmailHelper::processEmails($this->emailData['cc'] ?? '');
            $bccEmails = EmailHelper::processEmails($this->emailData['bcc'] ?? '');

            $totalRecipients = count($toEmails) + count($ccEmails) + count($bccEmails);

            if ($totalRecipients === 0) {
                Log::warning("No valid recipients found for {$this->logContext}");
                return;
            }

            // إذا كان العدد كبير، قسم إلى دفعات
            if ($totalRecipients > $this->batchSize) {
                $this->sendInBatches($toEmails, $ccEmails, $bccEmails);
            } else {
                $this->sendSingleEmail($toEmails, $ccEmails, $bccEmails);
            }

            Log::info("{$this->logContext} job completed successfully");

        } catch (\Exception $e) {
            Log::error("Failed to send {$this->logContext}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // إعادة رمي الخطأ للـ retry mechanism
        }
    }

    /**
     * إرسال إيميل واحد
     */
    private function sendSingleEmail(array $toEmails, array $ccEmails, array $bccEmails): void
    {
        $emailData = [
            'to' => implode(',', $toEmails),
            'cc' => implode(',', $ccEmails),
            'bcc' => implode(',', $bccEmails)
        ];

        EmailHelper::sendEmail($emailData, $this->mailClass, $this->logContext);
    }

    /**
     * إرسال الايميلات في دفعات
     */
    private function sendInBatches(array $toEmails, array $ccEmails, array $bccEmails): void
    {
        // تقسيم BCC emails إلى دفعات (الأهم)
        $bccBatches = array_chunk($bccEmails, $this->batchSize);
        
        foreach ($bccBatches as $index => $bccBatch) {
            $emailData = [
                'to' => $index === 0 ? implode(',', $toEmails) : '', // TO فقط في الدفعة الأولى
                'cc' => $index === 0 ? implode(',', $ccEmails) : '', // CC فقط في الدفعة الأولى
                'bcc' => implode(',', $bccBatch)
            ];

            EmailHelper::sendEmail($emailData, $this->mailClass, "{$this->logContext} - Batch " . ($index + 1));

            // تأخير بسيط بين الدفعات
            if ($index < count($bccBatches) - 1) {
                sleep(config('email_settings.delay_between_emails', 1));
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job failed permanently for {$this->logContext}", [
            'error' => $exception->getMessage(),
            'email_data' => $this->emailData
        ]);
    }
}
