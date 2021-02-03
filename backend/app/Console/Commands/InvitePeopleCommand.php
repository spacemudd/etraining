<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Models\Back\Company;
use App\Models\Back\Trainee;
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
     */
    public function handle()
    {
        //$company = Company::where('name_en', 'PBC')->firstOrFail();

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

        $companies = Company::whereNotIn('id',
            ['a0bc0fcb-20e3-45a3-a057-be5d47d26d19', '6d0a6fc0-4b5e-491d-97cc-b70b1f72774f']
        )->get();


        $traineesCount = Trainee::whereIn('company_id', $companies->pluck('id'))->count();
        $bar = $this->output->createProgressBar($traineesCount);
        $bar->start();

        foreach ($companies as $company) {
            foreach ($company->trainees as $trainee) {
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
                    Log::info('Failed validation for user: '.$trainee->email);
                    continue;
                    throw $e;
                }

                try {
                    Notification::send($user, new TraineeSetupAccountNotification());
                } catch (\Exception $e) {
                    Log::info('Failed for user: '.$trainee->email);
                    continue;
                    throw $e;
                }

                sleep(1);
                $bar->advance();
            }
        }
        
        $bar->finish();
        return 1;
    }
}
