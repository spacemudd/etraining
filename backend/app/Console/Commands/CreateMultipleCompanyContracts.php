<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use App\Models\Back\Instructor;
use App\Models\Back\TraineeGroup;
use App\Models\Back\Trainee;

class CreateMultipleCompanyContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example usage:
     * php artisan companies:create-contracts --companies=1,2,3 --instructor=5 --group=7
     *
     * @var string
     */
    protected $signature = 'companies:create-contracts {--companies=} {--instructor=} {--group=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إنشاء عقد لكل شركة في القائمة وربطه بمدرب وشعبة محددين';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $companies = Company::all();
        $instructorsByGroup = [
            'ab64a42c-77ea-43fa-838d-b9e02d81f9ea' => 'bd841a1a-b4e3-4e18-9474-1a35692dd294',
            '7567725f-f0ac-46fd-8ac4-72b26e21f4e2' => 'bd841a1a-b4e3-4e18-9474-1a35692dd294',
            'e7a256f6-1913-47df-a1c2-10c174bfbf5f' => 'a476dfe7-5a61-4860-b6f1-2a5aae5310b2',
            '47396796-9ee4-41d0-a069-179ea1b83a56' =>'bd841a1a-b4e3-4e18-9474-1a35692dd294',
            '3a021404-4876-4a5d-b889-59889aa19256' =>'a476dfe7-5a61-4860-b6f1-2a5aae5310b2',  
        ];
        $createdContracts = 0;
        $companyCount = 0;
        foreach ($companies as $company) {
            $companyCount++;
            $this->info('جاري معالجة الشركة: ' . $company->name_ar . ' (ID: ' . $company->id . ')');
            $groups = TraineeGroup::where('company_id', $company->id)->get();
            $groupCount = 0;
            foreach ($groups as $group) {
                $groupCount++;
                $this->info('  جاري معالجة الشعبة: ' . $group->name . ' (ID: ' . $group->id . ')');
                if (!isset($instructorsByGroup[$group->id])) {
                    $this->warn("  لا يوجد مدرب محدد للشعبة: {$group->name} في الشركة: {$company->name_ar}");
                    continue;
                }
                $instructorId = $instructorsByGroup[$group->id];
                $instructor = Instructor::find($instructorId);
                if (!$instructor) {
                    $this->error("  المدرب بالمعرف {$instructorId} غير موجود!");
                    continue;
                }
                $contract = new CompanyContract();
                $contract->company_id = $company->id;
                $contract->team_id = $company->team_id;
                $contract->contract_starts_at = now();
                $contract->save();
                $contract->instructors()->attach($instructor->id);
                Trainee::where('company_id', $company->id)
                    ->where('trainee_group_id', $group->id)
                    ->update([
                        'instructor_id' => $instructor->id
                    ]);
                $createdContracts++;
                $this->info("    ✅ تم إنشاء عقد للشركة: {$company->name_ar} - الشعبة: {$group->name} وربطه بالمدرب: {$instructor->name} بنجاح.");
            }
            $this->info("  عدد الشعب المعالجة لهذه الشركة: $groupCount");
        }
        $this->info('تمت العملية بنجاح لجميع الشركات.');
        $this->info('عدد الشركات التي تمت معالجتها: ' . $companyCount);
        $this->info('عدد العقود التي تم إنشاؤها فعلياً: ' . $createdContracts);
        return 0;
    }
} 