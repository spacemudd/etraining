<?php

namespace App\Jobs;

use App\Models\Back\CertificatesImport;
use App\Models\Back\CertificatesImportsRow;
use App\Models\Back\TraineeCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IssueCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $import;

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
        foreach ($this->import->rows as $row) {
            $certificate = $this->issue_certificate($row);
            $certificate->send_email();
            usleep(400);
        }
        $this->import->status = CertificatesImport::STATUS_SENT;
        $this->import->save();
    }

    public function issue_certificate(CertificatesImportsRow $row): TraineeCertificate
    {
        return TraineeCertificate::create([
            'course_id' => $this->import->course_id,
            'trainee_id' => $row->trainee_id,
        ]);
    }
}
