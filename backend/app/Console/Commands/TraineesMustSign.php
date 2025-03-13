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
                '1099219964',
                '1090804301',
                '1094519459',
                '1119061255',
                '1035724929',
                '1076925021',
                '1079775498',
                '1109298792',
                '1094189741',
                '1056018623',
                '1115220681',
                '1095959365',
                '1114414483',
                '1067297083',
                '1126452232',
                '1105781882',
                '1099412965',
                '1055046310',
                '1044671715',
                '1081911032',
                '1106157520',
                '1087866255',
                '1130862186',
                '1142458262',
                '1094824206',
                '1117723278',
                '1100063864',
                '1104444169',
                '1073648311',
                '1104582851',
                '1092628005',
                '1120560675',
                '1115636043',
                '1085760823',
                '1101076550',
                '1074502640',
                '1037646302',
                '1046221592',
                '1113608028',
                '1101254033',
                '1111701395',
                '1081663948',
                '١١٠٩٥٩٧٣٥٩',
                '1128025648',
                '1075040772',
                '1070105927',
                '1086140033',
                '1074554815',
                '1128508916',
                '1117510170',
                '1114526633',
                '1090659929',
                '1128018189',
                '1096054059',
                '1070418486',
                '1038744593',
                '1093396206',
                '1054352313',
                '1030846818',
        ];

        $updatedCount = Trainee::whereIn('identity_number', $identityIds)
                              ->update(['must_sign' => true]);


        $this->info("succefully updated {$updatedCount} trainees");



    }
}
