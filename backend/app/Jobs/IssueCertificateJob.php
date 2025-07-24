<?php

namespace App\Jobs;

use App\Mail\TraineeCertificateMail;
use App\Models\Back\CertificatesImport;
use App\Models\Back\CertificatesImportsRow;
use App\Models\Back\TraineeCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail as MailFacade;

class IssueCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $import;

    public $timeout = 21600;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CertificatesImport $import)
    {
        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $success = 0;
        $fail = 0;
        $start = now();
        foreach ($this->import->rows as $row) {
            try {
                if (!$this->alreadySentTo($row) && $row->pdf_path) {
                    $pdfContent = Storage::disk('s3')->get($row->pdf_path);
                    MailFacade::to($row->trainee->email)
                        ->queue((new \App\Mail\TraineeCertificateMail(null))
                            ->subject('شهادة تدريبية')
                            ->attachData($pdfContent, basename($row->pdf_path), ['mime' => 'application/pdf'])
                        );
                    $row->sent_at = now();
                    $row->save();
                    $success++;
                }
            } catch (\Exception $e) {
                $fail++;
                \Log::error('Failed to send certificate: '.$e->getMessage());
            }
            usleep(400);
        }
        $this->import->status = CertificatesImport::STATUS_SENT;
        $this->import->save();
        $end = now();
        // Send summary email to admin
        MailFacade::raw(
            "The process was complete\nfailed count: {$fail}\nsuccess count: {$success}\ncourse: {$this->import->course_id}\nstarted_at: {$start}\nended_at: {$end}",
            function ($message) {
                $message->to('shafiqalshaar@adv-line.com')
                    ->subject('Certificate Import Process Complete');
            }
        );
    }

    public function issue_certificate(CertificatesImportsRow $row): TraineeCertificate
    {
        return TraineeCertificate::create([
            'course_id' => $this->import->course_id,
            'trainee_id' => $row->trainee_id,
        ]);
    }

    public function alreadySentTo(CertificatesImportsRow $row)
    {
        return CertificatesImportsRow::where('trainee_id', $row->trainee_id)
            ->where('course_id', $this->import->course_id)
            ->whereNotNull('sent_at')
            ->exists();
    }
}
