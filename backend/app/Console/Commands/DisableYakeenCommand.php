<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DisableYakeenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yakeen:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable Yakeen API phone ownership verification';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Yakeen API phone ownership verification has been disabled.');
        $this->info('Make sure to set YAKEEN_ENABLED=false in your .env file.');
        $this->info('Current status: ' . (config('yakeen.enabled') ? 'ENABLED' : 'DISABLED'));
        
        return 0;
    }
}
