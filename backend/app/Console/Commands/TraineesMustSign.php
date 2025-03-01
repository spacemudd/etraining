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
            '1126452232',
            '1104060304',
            '1105781882',
            '١٠٢٢٦٥٨٧٣٤',
            '1069547345',
            '1099412965',
            '1117280949',
            '1055046310',
            '1105391484',
            '1127900676',
            '1044671715',
            '1081911032',
            '١٠٦٧٥٣٣٦٧٧',
            '1019993045',
            '1113062630',
            '1106157520',
            '1095229454',
            '1078754767',
            '1078691274',
            '1104509870',
            '1087866255',
            '1130862186',
            '1126123015',
            '1142458262',
            '1094824206',
            '1100476777',
            '1117723278',
            '1088695273',
            '1100063864',
            '1008177998',
            '1104444169',
            '1056561929',
            '1096818537',
            '1073648311',
            '1134226495',
            '1072445917',
        ];

        $updatedCount = Trainee::whereIn('identity_number', $identityIds)
                              ->update(['must_sign' => true]);


        $this->info("succefully updated {$updatedCount} trainees");

    }
}
