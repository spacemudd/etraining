<?php

namespace App\Console\Commands;

use App\Jobs\CompanyTraineeLinkAuditJob;
use Illuminate\Console\Command;

class CompanyTraineeSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:company-trainee-snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves a list of all trainees attached to company';

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
        dispatch(new CompanyTraineeLinkAuditJob());
        return 1;
    }
}
