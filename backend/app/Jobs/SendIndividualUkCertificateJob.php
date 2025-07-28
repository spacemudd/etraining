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
                Mail::to($row->trainee->email)
                    ->send(new \App\Mail\UkCertificateMail($pdfContent, basename($row->pdf_path), $row->trainee));
                
                $row->update([
                    'sent_at' => now(),
                    'status' => 'sent',
                ]);
            } else {
                throw new \Exception('PDF content is empty or could not be retrieved from S3');
            }
        } catch (\Exception $e) {
            $row->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            \Log::error('Failed to send UK certificate for row ' . $this->rowId . ': ' . $e->getMessage());
        }
    }
} 