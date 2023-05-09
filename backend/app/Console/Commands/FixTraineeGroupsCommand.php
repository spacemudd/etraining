<?php

namespace App\Console\Commands;

use App\Models\Back\TraineeGroup;
use App\Models\Back\Trainee;
use App\Models\Team;
use Illuminate\Support\Facades\DB;


use Illuminate\Console\Command;


class FixTraineeGroupsCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:fix-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command fixes the groups of all trainees that are currently attending the course of the instructor';


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
        //$traineesByCompany =
        //$traineesIds = Trainee::whereIn('company_id', [
        //    '248267f6-473b-4e75-af7b-da9416c233e1',
        //    'ac7393a9-d56d-49ae-9c43-a9031f244416',
        //    'b5d59d5a-b899-4919-bb74-43d0080b3800',
        //    '2fdbdf92-5095-41a9-a880-85d6ed2c3c3c',
        //    'e197d820-1ad3-4953-a22a-85a1aedf8844',
        //    'd01777d7-05a1-4edb-bd75-c60ddb5abb85',
        //    '1a4cf028-a709-477f-93df-2619334a06b5',
        //    'dd10ed03-f19f-4a3d-bc33-13ef794e7f0f',
        //    'a5e49b9c-910b-444b-8d37-ac190233e219',
        //    '9079899a-ab5a-4bc3-b513-9e3ff2ef2924',
        //    '669ba6b3-3ad4-4323-a312-16005ac11482',
        //    '9b4bf4c9-f789-4e55-bac5-ee2a1d79ad48',
        //    '5d6099c9-8bd7-4a49-b819-811d727bac70',
        //    '7038b5b6-2f08-4471-9a55-80ff8006d898',
        //    '5db0c4d0-5d78-4221-b860-ed33af620f52',
        //    '44aa3990-41f9-416b-988e-e919a811ee17',
        //    '26e3b0d3-a75d-4c62-b94a-0a104e1c6e9e',
        //    '6e61eded-aa67-419c-ada3-a6182214b361',
        //    '41c6b006-8807-4a93-b1b3-ebe485dca96e',
        //    '464a8f7b-7c24-4711-9d60-79b8b7e53518',
        //    '0b5f4c6b-9257-4512-a8ff-e51e3bc9f158',
        //    'c6e5722a-1cfe-4838-811a-e3397369a897',
        //    'c07e1fe3-4120-4c4e-88ec-3d85a30faefc',
        //    'ff7447d6-dfb6-429d-9d9e-18cabd486c43',
        //    'a0bc0fcb-20e3-45a3-a057-be5d47d26d19',
//        ]);

        //dd($traineesIds);


        DB::beginTransaction();
//        $oldGroup = TraineeGroup::where('id', 'e7a256f6-1913-47df-a1c2-10c174bfbf5f')->first();
//        //dd(count($oldGroup->trainees()->pluck('trainees.id')));
//        //$oldGroup->trainees()->sync($tra)
//
//        $allTraineeIds = Trainee::where('company_id', '7038b5b6-2f08-4471-9a55-80ff8006d898')->pluck('id');
//
//        $oldGroup->trainees()->attach($allTraineeIds);

        $Trainees = Trainee::where('company_id', 'bf4340d7-8058-4cfa-93d4-fdb1bcc6465e')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8108f6e9-6c82-4792-8cb7-7a9bb4c8a008',
            ]);
        }
        $Trainees = Trainee::where('company_id', 'd6ff80d3-737b-48aa-9e09-7887beb1e2c0')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '03b831ae-7621-4910-9093-24a9ba11febf',
            ]);
        }
        $Trainees = Trainee::where('company_id', '13e82fdc-e51a-430c-9c65-34040ef51306')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8108f6e9-6c82-4792-8cb7-7a9bb4c8a008',
            ]);
        }

        DB::commit();

        $this->info('Done!');

        return 1;
    }
}
