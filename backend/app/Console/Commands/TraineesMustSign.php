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
            '1092534294',
            '1093353223',
            '1073668764',
            '1092991908',
            '1092301942',
            '1093991550',
            '1087024350',
            '1087834816',
            '1097395188',
            '1101523072',
            '1078005277',
            '1094519459',
            '1123105510',
            '1124290642',
            '1113009672',
            '1075470714',
            '1119061255',
            '1035724929',
            '1033604164',
            '1095933659',
            '1127361853',
            '1127430336',
            '1079026850',
            '1120749377',
            '1076925021',
        ];

        $updatedCount = Trainee::whereIn('identity_number', $identityIds)
                              ->update(['must_sign' => true]);


        $this->info("succefully updated {$updatedCount} trainees");

    }
}
