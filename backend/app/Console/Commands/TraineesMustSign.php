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
            '1023747544',
            '1091320158',
            '1088695273',
            '1044259743',
            '1100832623',
            '1090804301',
            '1115757823',
            '1078213129',
            '1107583856',
            '1087024350',
            '1089853475',
            '1097723553',
            '1108866771',
            '1093083572',
            '1100345683',
            '1123105510',
            '1098963703',
            '1057469437',
            '1092522877',
            '1087440713',
            '1090497064',
            '1127361853',
            '1121444630',
            '1072445602',
            '1090065994',
            '1079026850',
            '1102044169',
            '1128236898',
            '1068732500',
            '1091898567',
            

        ];

        $updatedCount = Trainee::whereIn('identity_number', $identityIds)
                              ->update(['must_sign' => true]);


        $this->info("succefully updated {$updatedCount} trainees");



    }
}
