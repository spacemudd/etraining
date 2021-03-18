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
            966506700797,
            966502227742,
            966500727810,
            966555651997,
            966551617156,
            966551771373,
            966532192961,
            966508725745,
            966502784380,
            966532686682,
            966509677608,
            966534048039,
            966559456371,
            966533147228,
            966530822204,
            966538653461,
            966554551246,
            966559968647,
            966551516060,
            966509102113,
            966532758189,
            966506237235,
            966507042039,
            966502819809,
            966534551332,
            966536810251,
            966555551050,
            966556647876,
            966501232329,
            966532201191,
            966552261097,
            966502768320,
            966550328208,
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

بالاشارة الى عقد التدريب المبرم معكم بمركز احترافية المدرب ونظراً لعدم سدادكم المستحق المالي عن شهر فبراير ٢٠٢٠و نظرا لعدم سدادكم المستحق المالي وفقا للعقد المبرم نفيدكم بانه سوف يتم رفع دعوى قضائية تجاهكم بالمحكمه العامة نظرا لعدم السداد',
                    'عزيزتي المتدربة
بالاشارة الى عقد التدريب المبرم معكم بمركز احترافية المدرب ونظراً لعدم سدادكم المستحق المالي عن شهر فبراير ٢٠٢٠و نظرا لعدم سدادكم المستحق المالي وفقا للعقد المبرم نفيدكم بانه سوف يتم رفع دعوى قضائية تجاهكم بالمحكمه العامة نظرا لعدم السداد'
                ));
            }
        }

        return 1;
    }
}
