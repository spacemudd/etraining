<?php

namespace App\Console\Commands;

use App\Models\Back\TraineeGroup;
use App\Models\Back\Trainee;
use App\Models\Team;
use Illuminate\Support\Facades\DB;


use Illuminate\Console\Command;


class BlockTraineesCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'block-trainee';

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

        DB::beginTransaction();

        $Trainees = Trainee::where('company_id', 'bf4340d7-8058-4cfa-93d4-fdb1bcc6465e')->get();
        foreach ($Trainees as $Trainee) {
            $Trainee->update([
                'deleted_remark' => 'عدم التسجيل من الشركة '
            ]);
            $Trainee->suspended_at = now()->setSecond(0);

            $Trainee->delete();
            $Trainee->save();
        }
        DB::commit();

        $this->info('Done!');

        return 1;
    }
}
