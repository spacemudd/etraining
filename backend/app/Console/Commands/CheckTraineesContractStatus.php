<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Back\Trainee;
use App\Http\Controllers\ZohoSignController;
use Illuminate\Support\Facades\Log;

class CheckTraineesContractStatus extends Command
{
    protected $signature = 'zoho:check-contracts';
    protected $description = 'تحقق من حالة عقود جميع المتدربين';

    public function handle()
    {
        $zohoController = new ZohoSignController();
        $trainees = Trainee::whereNotNull('zoho_contract_id')->get();

        foreach ($trainees as $trainee) {
            $result = $zohoController->adminCheckContractStatusForTrainee($trainee);
            $this::info("checked contract for trainee {$trainee->id}" . json_encode($result));
            // Log::info("Checked contract for trainee {$trainee->id}: " . json_encode($result));
            
        }

        $this->info('تم التحقق من جميع عقود المتدربين.');
    }
}
