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

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 1;

    protected $ukCertificate;

    public function __construct(UkCertificate $ukCertificate)
    {
        $this->ukCertificate = $ukCertificate;
    }

    public function handle()
    {
        $start = now();

        // Dispatch individual jobs for each certificate
        foreach ($this->ukCertificate->rows()->whereNotNull('trainee_id')->where('status', 'pending')->get() as $row) {
            if ($row->pdf_path) {
                dispatch(new SendIndividualUkCertificateJob($row->id));
            }
        }

        $end = now();

        // Send summary email to admin
        Mail::raw(
            "The UK certificate process has been queued\ncourse: {$this->ukCertificate->course_id}\nstarted_at: {$start}\nqueued_at: {$end}",
            function ($message) {
                $message->to(['shafiqalshaar@adv-line.com', 'mashael.a@hadaf-hq.com'])
                    ->subject('UK Certificate Process Queued');
            }
        );
    }
}
