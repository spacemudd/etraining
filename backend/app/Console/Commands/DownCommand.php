<?php

namespace App\Console\Commands;

use App\Classes\CacheKeys;
use Cache;
use Illuminate\Foundation\Console\DownCommand as NativeDownCommand;

class DownCommand extends NativeDownCommand
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Cache::forever(CacheKeys::MAINTENANCE_PAYLOAD, json_encode($this->getDownFilePayload()));
        $this->comment('Application is now in maintenance mode.');
        return 1;
    }
}
