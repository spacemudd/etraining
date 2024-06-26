<?php

namespace App\Console\Commands;

use App\Models\Back\Audit;
use App\Models\Back\Trainee;
use App\Models\User;
use Illuminate\Console\Command;

class AddDeletedByUserInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:suspended';

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
        $count = Trainee::withoutGlobalScopes()
            ->whereNull('deleted_by_id')
            ->whereNotNull('deleted_at')->count();
        $current = 0;
        $this->info("{$current} / {$count}");
        Trainee::withoutGlobalScopes()->whereNull('deleted_by_id')->whereNotNull('deleted_at')->chunk(200, function ($trainees) use (&$count, &$current) {
           foreach ($trainees as $trainee) {
               $lastAudit = Audit::where('auditable_type', Trainee::class)
                   ->where('auditable_id', $trainee->id)
                   ->where('event', 'deleted')
                   ->orderBy('created_at', 'desc')
                   ->first();

               if ($lastAudit) {
                   if (User::where('id', $lastAudit->user_id)->exists()) {
                        $trainee->deleted_by_id = $lastAudit->user_id;
                        $trainee->save(['timestamps' => false]);
                   }
               }

               $current++;
               $this->info("{$current} / {$count}");
           }
        });

        return 1;
    }
}
