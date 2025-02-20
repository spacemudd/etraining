<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Back\Trainee; 

class TraineesMustSign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainees:must-sign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'assign must_sign field to true';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $identityIds = [
            '1119779252',
            '1090804301'
        ];

        $updatedCount = Trainee::whereIn('identity_id', $identityIds)
                              ->update(['must_sign' => true]);


        $this->info("succefully updated {$updatedCount} trainees");

    }
}
