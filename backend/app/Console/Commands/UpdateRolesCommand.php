<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Services\RolesService;
use Illuminate\Console\Command;

class UpdateRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $teams = Team::get();

        foreach ($teams as $team) {
            app()->make(RolesService::class)->seedRolesToTeam($team);
        }

        return 1;
    }
}
