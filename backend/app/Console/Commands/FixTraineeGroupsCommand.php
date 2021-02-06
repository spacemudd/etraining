<?php

namespace App\Console\Commands;

use App\Models\Back\TraineeGroup;
use App\Models\Back\Trainee;
use Illuminate\Support\Facades\DB;


use Illuminate\Console\Command;


class FixTraineeGroupsCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'etraining:fix-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command fixes the groups of all trainees that are currently attending the course of the instructor, العذوب';


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
        $this->info('Creating the trainee group if not existed');


        $trainee_groups = ['مجموعة شركة ميادر', 'مجموعة شركة دار المعدن', 'مجموعة مراسة', 'مجموعة شركة انتربلاست', 'مجموعة شركة المهباج', 'مجموعة مؤسسة الشفاء', 'شابورجي _ مجموعة 1 _ 87 متدربة', 'مجموعة شركة طيبة' , 'مجموعة شركة صحتين', 'مجموعة شركة الفانوس', 'مجموعة شركة الفتوح', 'مجموعة مصنع فادن', 'مجموعة مؤسسة اطهر', 'مجموعة شركة لوازم المكتب', 'مجموعة شركة معن الجاسر', 'مجموعة شركة سبك'];


        DB::beginTransaction();


        $targetTrainees = Trainee::whereHas('trainee_group', function($q) {
            $trainee_groups = ['مجموعة شركة ميادر', 'مجموعة شركة دار المعدن', 'مجموعة مراسة', 'مجموعة شركة انتربلاست', 'مجموعة شركة المهباج', 'مجموعة مؤسسة الشفاء', 'شابورجي _ مجموعة 1 _ 87 متدربة', 'مجموعة شركة طيبة' , 'مجموعة شركة صحتين', 'مجموعة شركة الفانوس', 'مجموعة شركة الفتوح', 'مجموعة مصنع فادن', 'مجموعة مؤسسة اطهر', 'مجموعة شركة لوازم المكتب', 'مجموعة شركة معن الجاسر', 'مجموعة شركة سبك'];
            $q->whereIn('name', $trainee_groups );
        })->get();


        $oldGroups = TraineeGroup::whereIn('name', $trainee_groups)->get();
        foreach ($oldGroups as $oldGroup) {
            $oldGroup->trainees()->detach();
        };


        $targetGroup = TraineeGroup::firstOrCreate([
            'name' => 'مجموعة العذوب',
        ]);

        foreach ($targetTrainees as $trainee) {
            $targetGroup->trainees()->attach($trainee->id);
        }

        DB::commit();


        $this->info('Done!');

        return 1;
    }
}
