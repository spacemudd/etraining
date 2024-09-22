<?php

namespace App\Console\Commands;

use App\Jobs\VerifyPhoneOwnershipJob;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
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
     * @throws \Throwable
     */
    public function handle()
    {
        Trainee::chunk(100, function($trainees) {
            foreach ($trainees as $trainee) {
                usleep(500);
                VerifyPhoneOwnershipJob::dispatch($trainee);
            }
        });

        return 1;
    }
}
