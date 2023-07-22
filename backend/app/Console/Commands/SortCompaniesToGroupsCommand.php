<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Models\Back\TraineeGroup;
use Illuminate\Console\Command;

class SortCompaniesToGroupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:sort-into-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sort companies into groups';

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
        // 1,2,3,4,5,6,7,8,9,10,11,12,13
        $groupNumbers = [1,2,3,4,5,6,7,8,9,10,11,12,13];
        $groups = [];
        foreach ($groupNumbers as $number) {
            $groups[] = TraineeGroup::where('name', 'شعبة ' . $number)
                ->first()
                ->id;
        }

        // Get all companies then assign them a \App\Models\Back\TraineeGroup where it has space based on the count of trainees in the group must not exceed 1000 in total.
        $companies = Company::get();

        $bar = $this->output->createProgressBar(count($companies));
        $bar->start();
        foreach ($companies as $key => $company) {
            $traineeGroups = TraineeGroup::whereIn('id', $groups)
                ->withCount('trainees')
                ->orderBy('trainees_count', 'desc')
                ->get();

            $companyTraineesCount = $company->trainees()->count();

            foreach ($traineeGroups as $group) {
                if ($group->trainees_count + $companyTraineesCount > 1000) {
                    $this->info('Skipping company ' . $company->name . ' because it has ' . $companyTraineesCount . ' trainees and group ' . $group->name . ' has ' . $group->trainees_count . ' trainees.');
                    continue;
                }
                $company->trainee_group_id = $group->id;
                $company->save();
            }
            $bar->advance();
        }
        $bar->finish();

        return 1;
    }
}
