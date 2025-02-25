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
           '1112465065',
           '1109298792',
           '1072134768',
           '1094189741',
           '1090065994',
           '1125877876',
           '1112975998',
           '1056018623',
           '1060988944',
           '1115220681',
           '1049788803',
           '1111164891',
           '1089727141',
           '1125960615',
           '1077188306',
           '1095959365',
           '1114414483',
           '1124006592',
           '1089853475',
           '1067297083',
           '1078047733',
           '1071944662',
        ];

        $updatedCount = Trainee::whereIn('identity_number', $identityIds)
                              ->update(['must_sign' => true]);


        $this->info("succefully updated {$updatedCount} trainees");

    }
}
