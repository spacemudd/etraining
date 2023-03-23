<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Models\Back\Company;
use App\Models\InboxMessage;
use App\Notifications\TraineeSetupAccountNotification;
use Illuminate\Console\Command;
use Notification;

class InviteUsersToApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:invite';

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
        $company = Company::find('6e835eae-4d70-4206-ae95-eb830f3e99ad');
        foreach ($company->trainees as $trainee) {
            if ($trainee->user) {
                continue;
            }

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
                dd([$e->getMessage(), $trainee->toArray()]);
            }

            Notification::send($user, new TraineeSetupAccountNotification());

            $message = new InboxMessage();
            $message->team_id = $trainee->team_id;
            $message->body = 'لقد تم قبولك في منصة التدريب';
            $message->to_id = $user->id;
            $message->is_system_message = true;
            $message->save();
        }
        return 1;
    }
}
