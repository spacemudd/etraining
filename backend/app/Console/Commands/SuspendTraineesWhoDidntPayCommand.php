<?php

namespace App\Console\Commands;

use App\Models\Back\Trainee;
use App\Models\Back\TraineeBlockList;
use Illuminate\Console\Command;

class SuspendTraineesWhoDidntPayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suspend:trainees';

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
        $trainees = Trainee::where('deleted_remark', 'عدم السداد')
            ->withTrashed()
            ->get();

        foreach ($trainees as $trainee) {
            $this->info($trainee->email);
            TraineeBlockList::firstOrCreate([
                'trainee_id' => $trainee->id,
            ], ['team_id' => $trainee->team_id,
                'trainee_id' => $trainee->id,
                'identity_number' => $trainee->identity_number,
                'name' => $trainee->name,
                'email' => $trainee->email,
                'phone' => $trainee->phone,
                'phone_additional' => $trainee->phone_additional,
                'reason' => $trainee->deleted_remark,
            ]);
            if ($trainee->user) {
                $trainee->user->delete();
            }
            $trainee->suspended_at = now()->setSecond(0);
            $trainee->save();
            $trainee->delete();
        }

        $trainees = Trainee::where('deleted_remark', 'عدم سداد المستحق المالي')
            ->withTrashed()
            ->get();

        foreach ($trainees as $trainee) {
            $this->info($trainee->email);
            TraineeBlockList::firstOrCreate([
                'trainee_id' => $trainee->id,
            ], [
                'team_id' => $trainee->team_id,
                'trainee_id' => $trainee->id,
                'identity_number' => $trainee->identity_number,
                'name' => $trainee->name,
                'email' => $trainee->email,
                'phone' => $trainee->phone,
                'phone_additional' => $trainee->phone_additional,
                'reason' => $trainee->deleted_remark,
            ]);
            if ($trainee->user) {
                $trainee->user->delete();
            }
            $trainee->suspended_at = now()->setSecond(0);
            $trainee->save();
            $trainee->delete();
        }

        return 1;
    }
}
