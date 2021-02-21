<?php

namespace App\Console\Commands;

use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use Illuminate\Console\Command;

class AdhocCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:adhoc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For random adhoc execution of commands';

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
        $tr = TraineeGroup::where('name', 'احترافية الأعمال - سمر')->first();
        $tr->name = 'مجموعة سمر';
        $tr->save();

        return 1;
    }
}
