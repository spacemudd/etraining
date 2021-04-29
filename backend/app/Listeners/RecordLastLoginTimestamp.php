<?php

namespace App\Listeners;

use App\Jobs\LoginTimestampJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecordLastLoginTimestamp
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        LoginTimestampJob::dispatch($event->user->id, now());
    }
}
