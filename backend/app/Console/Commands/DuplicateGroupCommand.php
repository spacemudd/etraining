<?php

namespace App\Console\Commands;

use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DuplicateGroupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainee:group';

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
        DB::beginTransaction();

        $records = TraineeGroup::where('name', 'تجريبية - نجوم الشاسعة')->get();

        foreach ($records as $record) {
            $newRecord = $record->replicate();
            $newRecord->save();
        }
        DB::commit();

        return 1;

    }
}
