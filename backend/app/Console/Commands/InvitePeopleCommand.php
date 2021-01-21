<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Models\Back\Company;
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
    protected $signature = 'etraining:invite';

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
        $company = Company::where('name_en', 'PBC')->firstOrFail();

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
                throw $e;
            }


            try {
                Notification::send($user, new TraineeSetupAccountNotification());
            } catch (\Exception $e) {
                Log::info('Failed for user: '.$trainee->email);
                throw $e;
            }

            sleep(1);
        }

        return 1;
    }
}
