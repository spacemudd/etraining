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

        $response = ElmYakeen::new()
            ->verifyOwnership($trainee->identity_number, $trainee->clean_phone);

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
}
