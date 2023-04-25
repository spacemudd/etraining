<?php

namespace App\Console\Commands;

use App\Models\Back\Trainee;
use App\Notifications\TraineePrivateMessage;
use Illuminate\Console\Command;

class CustomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:custom';

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
        $numbers = [
            966533208783,
            966534191077,
        ];

        foreach ($numbers as $number) {
            $trainee = Trainee::where('phone', $number)->first();
            if (!$trainee) {
                $this->info('Trainee not found: '.$number);
            } else {
                $this->info('Sent: '.$trainee->phone);
                $trainee->notify(new TraineePrivateMessage(
                    'سداد مستحقات التدريب',
                    'عزيزتي المتدربة

بالاشارة الى عقد التدريب المبرم معكم بشركة مركز احترافية التدريب ونظراً لعدم سدادكم المستحق المالي عن شهر فبراير 2021 و نظرا لعدم سدادكم المستحق المالي وفقا للعقد المبرم نفيدكم بانه سوف يتم رفع دعوى قضائية تجاهكم بالمحكمه العامة نظرا لعدم السداد',
                    'عزيزتي المتدربة
بالاشارة الى عقد التدريب المبرم معكم بشركة مركز احترافية التدريب ونظراً لعدم سدادكم المستحق المالي عن شهر فبراير 2021 و نظرا لعدم سدادكم المستحق المالي وفقا للعقد المبرم نفيدكم بانه سوف يتم رفع دعوى قضائية تجاهكم بالمحكمه العامة نظرا لعدم السداد'
                ));
            }
        }

        return 1;
    }
}
