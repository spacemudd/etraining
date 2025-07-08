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
        // جلب جميع الشركات في النظام
        $companies = Company::all();

        // تعريف المدربين حسب الشعب
        $instructorsByGroup = [
            'ab64a42c-77ea-43fa-838d-b9e02d81f9ea' => 'bd841a1a-b4e3-4e18-9474-1a35692dd294', // شعبة 4 -> مدرب معرفه 1
            '7567725f-f0ac-46fd-8ac4-72b26e21f4e2' => 'bd841a1a-b4e3-4e18-9474-1a35692dd294', // شعبة 3 -> مدرب معرفه 5
            'e7a256f6-1913-47df-a1c2-10c174bfbf5f' => 'a476dfe7-5a61-4860-b6f1-2a5aae5310b2',
            '47396796-9ee4-41d0-a069-179ea1b83a56' =>'bd841a1a-b4e3-4e18-9474-1a35692dd294',
            '3a021404-4876-4a5d-b889-59889aa19256' =>'a476dfe7-5a61-4860-b6f1-2a5aae5310b2',  
        ];

        foreach ($companies as $company) {
            // جلب جميع الشعب الموجودة في هذه الشركة
            $groups = TraineeGroup::where('company_id', $company->id)->get();
            CompanyContract::where('company_id', $company->id)->delete();
         
            foreach ($groups as $group) {
                // التحقق من وجود مدرب لهذه الشعبة
                if (!isset($instructorsByGroup[$group->id])) {
                    $this->warn("لا يوجد مدرب محدد للشعبة: {$group->name} في الشركة: {$company->name_ar}");
                    continue;
                }

                $instructorId = $instructorsByGroup[$group->id];
                $instructor = Instructor::find($instructorId);
                
                if (!$instructor) {
                    $this->error("المدرب بالمعرف {$instructorId} غير موجود!");
                    continue;
                }

                // حذف العقود السابقة لهذه الشركة والشعبة
                
                // إنشاء عقد جديد للشركة
                $contract = new CompanyContract();
                $contract->company_id = $company->id;
                $contract->team_id = $company->team_id;
                $contract->contract_starts_at = now();
                $contract->save();

                // ربط العقد بالمدرب
                $contract->instructors()->attach($instructor->id);

                // ربط متدربي هذه الشعبة بالمدرب
                Trainee::where('company_id', $company->id)
                    ->where('trainee_group_id', $group->id)
                    ->update([
                        'instructor_id' => $instructor->id
                    ]);

                $this->info("تم إنشاء عقد للشركة: {$company->name_ar} - الشعبة: {$group->name} وربطه بالمدرب: {$instructor->name} بنجاح.");
            }
        }
        
        $this->info('تمت العملية بنجاح لجميع الشركات والشعب.');
        return 0;
    }
} 