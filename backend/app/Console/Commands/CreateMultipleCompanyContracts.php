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
        // جلب معرفات الشركات باستخدام get()
        $companies = Company::whereHas('trainees', function ($query) {
            $query->where('trainee_group_id', 'ab64a42c-77ea-43fa-838d-b9e02d81f9ea');
             })->get();
        

        $instructorId = $this->option('instructor');
        $groupId = $this->option('group');

        $instructor = Instructor::find($instructorId);
        if (!$instructor) {
            $this->error("المدرب بالمعرف {$instructorId} غير موجود!");
            return 1;
        }

        $group = TraineeGroup::find($groupId);
        if (!$group) {
            $this->error("الشعبة بالمعرف {$groupId} غير موجودة!");
            return 1;
        }

        foreach ($companies as $company) {
            // حذف العقود السابقة للشركة
            CompanyContract::where('company_id', $company->id)->delete();
            
            $contract = new CompanyContract();
            $contract->company_id = $company->id;
            $contract->contract_starts_at = now();
            $contract->save();

            $contract->instructors()->attach($instructor->id);

            // ربط جميع متدربي الشركة بالشعبة والمدرب المحددين
            Trainee::where('company_id', $company->id)
                ->update([
                    //'trainee_group_id' => $group->id,
                    'instructor_id' => $instructor->id
                ]);

            // ربط جميع متدربي الشركة بالشعبة المحددة (اختياري)
            // App\Models\Back\Trainee::where('company_id', $company->id)->update(['trainee_group_id' => $group->id]);

            $this->info("تم إنشاء عقد للشركة: {$company->name_ar} وربطه بالمدرب والشعبة بنجاح.");
        }
        $this->info('تمت العملية بنجاح لجميع الشركات.');
        return 1;
    }
} 