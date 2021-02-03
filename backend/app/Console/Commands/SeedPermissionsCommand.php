<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Database\Seeders\PermissionsTableSeeder;
use Illuminate\Console\Command;

class SeedPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:perms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the new permissions for the platform';

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
        $this->info('Current number of permissions: '.Permission::count());
        $this->call('db:seed', ['--class' => PermissionsTableSeeder::class]);
        $this->info('Number of permissions after seeding: '.Permission::count());
        return 1;
    }
}
