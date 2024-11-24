<?php

namespace App\Jobs;

use App\Models\Back\Trainee;
use App\Models\Back\TraineeBlockList;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SuspendTraineesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $traineeIds;
    protected $reason;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param array $traineeIds
     * @param string $reason
     * @param int $userId
     */
    public function __construct(array $traineeIds, string $reason, int $userId)
    {
        $this->traineeIds = $traineeIds;
        $this->reason = $reason;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $trainees = Trainee::whereIn('id', $this->traineeIds)->get();

        foreach ($trainees as $trainee) {
            $trainee->update([
                'deleted_remark' => $this->reason,
                'suspended_at' => now()->setSecond(0),
                'deleted_by_id' => $this->userId,
            ]);

            TraineeBlockList::create([
                'trainee_id' => $trainee->id,
                'identity_number' => $trainee->identity_number,
                'name' => $trainee->name,
                'email' => $trainee->email,
                'phone' => $trainee->phone,
                'phone_additional' => $trainee->phone_additional,
                'reason' => $this->reason,
            ]);

            $trainee->delete();
        }
    }
}
س