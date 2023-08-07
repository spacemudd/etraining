<?php

namespace App\Console\Commands;

use App\Classes\CacheKeys;
use Cache;
use Illuminate\Foundation\Console\UpCommand as NativeUpCommand;

class UpCommand extends NativeUpCommand
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Cache::forget(CacheKeys::MAINTENANCE_PAYLOAD);
        $this->info('Application is now live');
        return 1;
    }
}
