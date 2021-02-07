<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoginTimestampJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    public $user_id;

    /**
     * @var Carbon
     */
    public $logged_at;

    /**
     * Create a new job instance.
     *
     * @param string $user_id
     * @param \Carbon\Carbon $logged_at
     */
    public function __construct(string $user_id, Carbon $logged_at)
    {
        $this->user_id = $user_id;
        $this->logged_at = $logged_at;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        User::withoutGlobalScopes()
            ->find($this->user_id)
            ->update([
                'last_login_at' => $this->logged_at,
            ]);
    }
}
