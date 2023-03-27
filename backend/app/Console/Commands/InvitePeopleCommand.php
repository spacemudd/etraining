<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use App\Notifications\TraineeSetupAccountNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class InvitePeopleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:invite {--trainee=}';

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
     * @throws \Exception
     */
    public function handle()
    {
        if ($trainee_id = $this->option('trainee')) {
            $trainee = Trainee::findOrFail($trainee_id);
            $this->info('Creating for user: '.$trainee->email);
            try {
                $user = (new CreateNewTraineeUser())->create([
                    'trainee_id' => $trainee->id,
                    'name' => $trainee->name,
                    'email' => $trainee->email,
                    'phone' => $trainee->phone,
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ]);
            } catch (\Exception $e) {
                Log::info('Failed validation for user: '.$trainee->email);
                throw $e;
            }

            try {
                Notification::send($user, new TraineeSetupAccountNotification());
            } catch (\Exception $e) {
                Log::info('Failed for user: '.$trainee->email);
                throw $e;
            }
            return 1;
        }


        $company = Company::where('id', '6e835eae-4d70-4206-ae95-eb830f3e99ad')->with('trainees')->first();
        $trainees = $company->trainees;

        $traineesCount = $trainees->count();
        $bar = $this->output->createProgressBar($traineesCount);
        $bar->start();

        Trainee::whereIn('id', $trainees->pluck('id'))->chunk(100, function($traineesCollection) use (&$bar) {
            foreach ($traineesCollection as $trainee) {
                if ($trainee->user_id) {
                    continue;
                }

                $this->info('Creating for user: '.$trainee->email);
                try {
                    $user = (new CreateNewTraineeUser())->create([
                        'trainee_id' => $trainee->id,
                        'name' => $trainee->name,
                        'email' => $trainee->email,
                        'phone' => $trainee->phone,
                        'password' => 'password',
                        'password_confirmation' => 'password',
                    ]);
                } catch (\Exception $e) {
                    $this->info('Failed validation for user: '.$trainee->email);
                    Log::info('Failed validation for user: '.$trainee->email);
                    Log::info($e->getMessage());
                    continue;
                    throw $e;
                }

                try {
                    Notification::send($user, new TraineeSetupAccountNotification());
                } catch (\Exception $e) {
                    $this->info('Failed sending notification for user: '.$trainee->email);
                    Log::info('Failed for user: '.$trainee->email);
                    Log::info($e->getMessage());
                    continue;
                    throw $e;
                }

            }

            sleep(61);
            $bar->advance(count($traineesCollection));
        });

        $bar->finish();


        return 1;
    }
}
