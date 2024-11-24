<?php

namespace App\Jobs;

use App\Models\Back\Trainee;
use App\Models\Back\TraineeBlockList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SuspendTraineesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $reason;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param string $reason
     */
    public function __construct(array $data, string $reason)
    {
        $this->data = $data;
        $this->reason = $reason;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reason = $this->reason;
        
        collect($this->data)->chunk(10)->each(function ($chunk) use ($reason) {
            DB::beginTransaction();
            try {
                $trainees = Trainee::whereIn('id', $chunk)->get();

                foreach ($trainees as $trainee) {
                    $trainee->deleted_remark = $reason;
                    $trainee->suspended_at = now()->setSecond(0);
                    $trainee->deleted_by_id = auth()->user()->id;
                    $trainee->save();

                    TraineeBlockList::create([
                        'trainee_id' => $trainee->id,
                        'identity_number' => $trainee->identity_number,
                        'name' => $trainee->name,
                        'email' => $trainee->email,
                        'phone' => $trainee->phone,
                        'phone_additional' => $trainee->phone_additional,
                        'reason' => $reason,
                    ]);

                    $trainee->delete();
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error suspending trainees: ' . $e->getMessage());
            }
        });
    }
}
