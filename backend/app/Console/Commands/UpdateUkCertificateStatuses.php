<?php

namespace App\Console\Commands;

use App\Models\Back\UkCertificate;
use Illuminate\Console\Command;

class UpdateUkCertificateStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uk-certificates:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status of UK certificates that have been sent but not properly marked as completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting UK certificate status update...');
        
        $certificates = UkCertificate::whereIn('status', ['processing', 'sending'])
            ->with('rows')
            ->get();
        
        $updated = 0;
        
        foreach ($certificates as $certificate) {
            $this->line("Checking certificate ID: {$certificate->id}");
            
            if ($certificate->checkAndUpdateCompletionStatus()) {
                $this->info("Updated certificate ID {$certificate->id} to status: {$certificate->status}");
                $updated++;
            }
        }
        
        $this->info("Completed! Updated {$updated} certificates.");
        
        return 0;
    }
}
