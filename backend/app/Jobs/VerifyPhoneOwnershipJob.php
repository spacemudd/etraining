<?php

namespace App\Jobs;

use App\Classes\ElmYakeen;
use App\Models\Back\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyPhoneOwnershipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $trainee_id;
    public $timeout = 5;
    public $tries = 1;
    protected $response;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $trainee_id)
    {
        $this->trainee_id = $trainee_id;
    }

    /**
     * Execute the job.
     *
     * @return int
     */
    public function handle()
    {
        $trainee = Trainee::withTrashed()->find($this->trainee_id);

        if (!$trainee->clean_phone) {
            return 1;
        }

        $response = ElmYakeen::new()
            ->verifyOwnership($trainee->identity_number, $trainee->clean_phone);

        $this->response = $response;

        if (! array_key_exists('code', $response)) {
            $trainee->phone_ownership_verified_at = now();
            $trainee->phone_is_owned = $response['isOwner'] ?? null;
        } else {
            $trainee->phone_ownership_verified_at = now();
            $trainee->phone_is_owned = false;
        }
        $trainee->save();

        $trainee->audits()->create([
            'event' => 'verify.ownership',
            'new_values' => $response,
        ]);

        return 1;
    }

    public function failed(\Throwable $exception)
    {
        \Log::error('Phone verification failed for trainee ID ' . $this->trainee_id, [
            'exception' => $exception->getMessage(),
            'response' => $this->response ?? null,
        ]);
    }
}
