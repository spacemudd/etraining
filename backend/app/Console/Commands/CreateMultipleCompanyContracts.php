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
        // ضع هنا معرفات الشركات
        $companyIds = [
            // مثال:
            // 1, 2, 3, 4, 5
        ];
        // $companyIds = explode(',', $this->option('companies')); // لم تعد مطلوبة
        $instructorId = $this->option('instructor');
        $groupId = $this->option('group');

        $instructor = Instructor::findOrFail($instructorId);
        $group = TraineeGroup::findOrFail($groupId);

        foreach ($companyIds as $companyId) {
            $company = Company::find($companyId);
            if (!$company) {
                $this->error("الشركة بالمعرف {$companyId} غير موجودة!");
                continue;
            }

            $contract = new CompanyContract();
            $contract->company_id = $company->id;
            $contract->contract_starts_at = now();
            $contract->save();

            $contract->instructors()->attach($instructor->id);

            // ربط جميع متدربي الشركة بالشعبة والمدرب المحددين
            Trainee::where('company_id', $company->id)
                ->update([
                    'trainee_group_id' => $group->id,
                    'instructor_id' => $instructor->id
                ]);

            // ربط جميع متدربي الشركة بالشعبة المحددة (اختياري)
            // App\Models\Back\Trainee::where('company_id', $company->id)->update(['trainee_group_id' => $group->id]);

            $this->info("تم إنشاء عقد للشركة: {$company->name_ar} وربطه بالمدرب والشعبة بنجاح.");
        }
        $this->info('تمت العملية بنجاح لجميع الشركات.');
        return 0;
    }
} 