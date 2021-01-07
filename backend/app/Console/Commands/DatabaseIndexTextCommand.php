<?php

namespace App\Console\Commands;

use App\Models\Back\Course;
use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DatabaseIndexTextCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all models for text searching';

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
        $this->info('Starting importing of all models');
        Artisan::call('scout:import', ['model' => Instructor::class]);
        Artisan::call('scout:import', ['model' => Trainee::class]);
        Artisan::call('scout:import', ['model' => Course::class]);
        $this->info('Completed importing of all models');
        return 1;
    }
}
