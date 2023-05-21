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

        $Trainees = Trainee::where('company_id', 'ac0f1b08-d055-4cef-959e-c8dff6e9f560')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '436c9f1c-feb5-4732-bfb8-a4cee365beb2')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '747ae3a0-2233-4e29-bf8c-9b888d16b733')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '7b7a37a6-1029-48e5-9f85-4f6d0776d3fe')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '355e7f25-c2f4-430e-b630-d06687603317')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', 'c1b6f2ed-086e-4101-a2d0-9c4e13e6890a')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '6ceb4ec5-977c-432b-bf9e-4ea2374d3f4d')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '4df7770e-88a3-4039-96fb-01882317a848')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '130efe6b-ae0d-49fe-9d16-3bf8dae8bce3')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }

        $Trainees = Trainee::where('company_id', '20e51612-80ff-418a-a0e5-5ca49f01b016')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }

        $Trainees = Trainee::where('company_id', '1bf9ad00-f154-47e6-acbb-b70586819c62')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }

        $Trainees = Trainee::where('company_id', 'ba199b21-d469-4c28-abcb-e1cf044659fc')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }

        $Trainees = Trainee::where('company_id', 'fc71a810-2c90-4f88-a005-a6d007c98210')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '65cc8df7-aff4-4e4b-abaf-9c7608036178')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '75a68be5-f16d-468b-8296-044ddb3d570a')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', 'b2ac1143-84bc-4598-92b1-c57ca96fdf28')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '4cd64200-5555-4d91-8054-e10043ec880c')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '65f572e6-0618-410d-90f2-3bf85f886263')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '6631c608-7e9f-48e6-9135-ae616dbc856d')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '95f6786c-9f76-4726-933a-e755d7ee922e')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', 'a23c3412-aa0a-41d1-843d-20aa0c84c577')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', 'e424576b-b741-4574-99d4-d77bc439ceb5')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '24fb3b8d-a37c-4e8d-b441-1296511b37f1')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', 'e844b51f-7808-48b0-a7de-3322ff2af546')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }
        $Trainees = Trainee::where('company_id', '1848acf2-71eb-4c2a-abe6-1fa733996386')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }

        $Trainees = Trainee::where('company_id', 'edcea22b-87b2-4369-8578-2b2382466cc0')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }

        $Trainees = Trainee::where('company_id', '786ae1a8-7371-4135-8f2e-a733aa2f491f')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }

        $Trainees = Trainee::where('company_id', 'cc1fd9d9-e820-4db8-a5a5-cff76d3c1277')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }

        $Trainees = Trainee::where('company_id', 'be26910d-dd66-411f-bf8e-137067d3cf5e')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'trainee_group_id' => '8e611f07-3e29-4ecf-a4c5-8fa83ac70b14',
            ]);
        }

        DB::commit();

        $this->info('Done!');

        return 1;
    }
}
