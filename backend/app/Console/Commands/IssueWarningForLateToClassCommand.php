<?php

namespace App\Console\Commands;

use App\Jobs\SendLateClassNotificationsJob;
use App\Models\Back\CourseBatchSession;
use Illuminate\Console\Command;

class IssueWarningForLateToClassCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:issue-warnings {--session=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        if ($session_id = $this->option('session')) {
            $session = CourseBatchSession::findOrFail($session_id);
            SendLateClassNotificationsJob::dispatch($session);
        }
        return 1;
    }
}
