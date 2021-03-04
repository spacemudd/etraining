<?php

namespace App\Console\Commands;

use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use Illuminate\Console\Command;
use DB;

class SyncTraineeGroupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:sync-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For the new ver where groups are assigned on the trainees table';

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
        $bar = $this->output->createProgressBar(Trainee::withTrashed()->count());
        \DB::transaction(function() use ($bar) {
            Trainee::withTrashed()->chunk(200, function($chunk) use ($bar) {
                foreach ($chunk as $trainee) {
                    $oldPivot = DB::table('trainee_group_trainee')
                        ->where('trainee_id', $trainee->id)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($oldPivot && $oldPivot->trainee_group_id) {
                        $trainee->trainee_group_id = $oldPivot->trainee_group_id;
                        $trainee->save();
                    }
                }

                $bar->advance($chunk->count());
            });
        });

        $bar->finish();
        return 1;
    }
}
