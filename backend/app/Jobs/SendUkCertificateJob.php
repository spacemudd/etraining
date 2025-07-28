<?php

namespace App\Jobs;

use App\Models\Back\UkCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendUkCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ukCertificate;

    public function __construct(UkCertificate $ukCertificate)
    {
        $this->ukCertificate = $ukCertificate;
    }

    public function handle()
    {
        $success = 0;
        $fail = 0;
        $start = now();

        foreach ($this->ukCertificate->rows()->whereNotNull('trainee_id')->get() as $row) {
            try {
                if ($row->status === 'pending' && $row->pdf_path) {
                    $pdfContent = Storage::disk('s3')->get($row->pdf_path);
                    
                    Mail::to($row->trainee->email)
                        ->queue((new \App\Mail\UkCertificateMail())
                            ->subject('شهادة تدريبية')
                            ->attachData($pdfContent, basename($row->pdf_path), ['mime' => 'application/pdf'])
                        );
                    
                    $row->update([
                        'sent_at' => now(),
                        'status' => 'sent',
                    ]);
                    
                    $success++;
                }
            } catch (\Exception $e) {
                $row->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $fail++;
                \Log::error('Failed to send UK certificate: ' . $e->getMessage());
            }
            
            // Small delay to avoid overwhelming the email service
            usleep(400);
        }

        $this->ukCertificate->update([
            'status' => UkCertificate::STATUS_SENT,
            'sent_count' => $success,
            'failed_count' => $fail,
            'completed_at' => now(),
        ]);

        $end = now();

        // Send summary email to admin
        Mail::raw(
            "The UK certificate process was complete\nfailed count: {$fail}\nsuccess count: {$success}\ncourse: {$this->ukCertificate->course_id}\nstarted_at: {$start}\nended_at: {$end}",
            function ($message) {
                $message->to('shafiqalshaar@adv-line.com')
                    ->subject('UK Certificate Process Complete');
            }
        );
    }
}
