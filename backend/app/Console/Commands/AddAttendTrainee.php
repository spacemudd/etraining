<?php

namespace App\Console\Commands;

use App\Models\Back\AttendanceReport;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\Trainee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddAttendTrainee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainees:add {trainee_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark attendances as absent between two dates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        DB::beginTransaction();
        $trainee_id = $this->argument('trainee_id');

        $records = AttendanceReportRecord::whereIn('id',
            [
                '34b6abbe-d83d-4679-8547-608607d98a1e',
                '21674ecf-f8f5-48fd-9a7f-1681bf6cc822',
                '96f676d7-78c6-4365-817a-6e7c2650677c',
                '04fcc8d1-bc94-4680-b4b5-e0ac3ed62891',
                '8684c8c0-f82a-43a0-aeb9-93210c7155f9',
                '613a62c3-b096-400a-99a6-4031211330fd',
                '913ed6db-b02c-4b3c-a7e3-11abb905c12c',
                '8a6dc1e5-a198-4a79-bfb2-9fa8d1a95b80',
                'ce133c16-b2ff-4216-83f0-2a49efa6a440',
                'b82c8971-89ee-43c2-8981-9b925c1602c0',
                '9d0a56c6-06d7-406c-bdae-9a18e446b75f',
                '5d853cae-8e7a-4c53-bfac-1e9e57e1698e',
                'f2436338-2e8c-45eb-81a7-e963a8090dd3',
                '7fbe5d35-f1fc-4b0d-85d4-b9fa687cbcf1',
                '8356951a-2b35-40a4-9323-4e6d6406ceb5',
                'a604d55d-0293-44f2-8d0b-ed8400537bfb',
                'eacb0da3-af29-4adc-a72f-deb3af69bd72',
                '894b4182-fa63-49d9-8c5f-33ea3ef34003',
                '3bd2113f-5ebd-49c2-b941-961c802c5b23',
                '1f0564f8-2e7e-41f7-b34e-c4a25e3f8fbe',
                '29cae30e-cbeb-4ea4-8baa-383180aa7100',
                'cc35f021-3f55-41b0-a0bd-623f2cad73ec',
                'fb7b2ded-8a5a-44e1-b2a7-939763ad1120',
                'bf99551f-ff4a-4647-a255-c4a6afd0e820',
                '4d3cc8b7-d968-47d6-bc57-19e73faa1950',
                '401e3f9a-2c25-4139-b7b0-e1e06919636f',
                '8cfa0472-d303-4200-bfe7-4ea2fe6654b5',

            ])->get();
        foreach ($records as $record) {
            $x = $record->replicate()
                ->fill(['trainee_id' => '2bf114ab-1000-4768-a443-c152b5164ae3'])
                ->save();
        }
//        $trainee = Trainee::withTrashed()->findOrFail($trainee_id);
//
//        $records = AttendanceReportRecord::where('trainee_id', $trainee->id);
//
//        $this->info('Found: '.$records->count());
        DB::commit();

//        DB::beginTransaction();
//        AttendanceReport::unguard();
//        foreach ($records as $record) {
//            $x = $record->replicate();
//            $x->trainee_id = '';
//            $x ->save();
//        }
//        AttendanceReport::reguard();
//        DB::commit();

        $this->info('Updated: '.$records->count());

        return $x;
    }
}
