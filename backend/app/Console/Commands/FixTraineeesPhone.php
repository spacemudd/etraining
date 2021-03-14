<?php

namespace App\Console\Commands;

use App\Models\Back\Trainee;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FixTraineeesPhone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:phones';

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
        $traineesCount = Trainee::candidates()->count();
        $bar = $this->output->createProgressBar($traineesCount);
        $bar->start();

        Trainee::candidates()->chunk(100, function($trainees) use ($bar) {
            foreach ($trainees as $trainee) {
                if (Str::startsWith('966', $trainee->phone)) {
                    continue;
                }

                if (Str::startsWith($trainee->phone, '05')) {
                    $trainee->phone = Str::replaceFirst('05', '9665', $trainee->phone);
                    $trainee->save();

                    if ($trainee->user) {
                        $user = $trainee->user;
                        $user->phone = $trainee->phone;
                        $user->save();
                    }
                }

                if (Str::startsWith($trainee->phone, '5')) {
                    $trainee->phone = Str::replaceFirst('5', '9665', $trainee->phone);
                    $trainee->save();

                    if ($trainee->user) {
                        $user = $trainee->user;
                        $user->phone = $trainee->phone;
                        $user->save();
                    }
                }
            }
            $bar->advance($trainees->count());
        });

        return 0;
    }
}
