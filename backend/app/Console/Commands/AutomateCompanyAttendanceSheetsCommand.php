<?php

namespace App\Console\Commands;

use App\Models\Back\Company;
use App\Services\CompanyAttendanceReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AutomateCompanyAttendanceSheetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company-attendance-sheets:start {--company_id=}';

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
                $names = [
'شركة لامار الخليجية' => 'Fk0sk1@hotmail.com',
'شركة النعمان إخوان العالمية للتجارة المحدودة' => 'manager@alnoman1.com',
'شركة شجرة الدر لتقديم الوجبات' => 'nono201262@hotmail.com',
'شركة الرك للمقاولات' => 'Bader.tuwayn@ruk-sa.com',
'شركة خالد ورعد عبدالعزيز السحيم للنقل' => 'info@al-suhaim.com',
'لشركة عبدالرحمن عثمان محمد العثمان وشريكه' => 'alothman.abdulaziz@gmail.com',
'ورشة كراج مرن لصيانة السيارات' => 'hussam@garage.sa',
'شركة خلدون بن مالك بنارس خان وشريكه' => 'm.babkeer@filemban.com',
'شركة مصنع السمو لإنتاج مياه الشرب المحدودة' => 'sadiqah@alsumou.com',
'ورشة الإتجاهات السته لصيانة السيارات' => 'shalabi1953@gmail.com',
'شركة وضوح الشرق للحديد' => 'Hr@besco.com.sa',
'مصنع مكيفات الراحة فرع شركة العمران للصناعة والتجارة' => 'm.alanzi@alomranco.com',
'شركة النافورة لإدارة الفنادق المحدوده' => 'aks@resorts.com.sa',
'شركة وثاق العالمية المحدودة' => 'Higher.managment@wethaq.sa',
'شركة مصنع محمد سالم العجيمي وشريكة المحدودة' => 'mohammed.daifallah@alojaimi.com',
'مؤسسة عماد عبدالجواد المحتسب للتجارة' => 'rebhi-khader@e-almohtaseb.com',
'شركة كيلة الشيف لتقديم الوجبات' => 'afalothman1991@gmail.com',
'شركة الهاجري لتجارة الأغذية' => 'Nasser@alhajrifood.com',
'شركة ايتاب الدولية للتجارة' => 'mohammed.alzahrani@loftstore.com',
'شركة اتمار للمقاولات' => 'Atmarest2005@gmail.com',
'شركة الكوفة للأغذية' => 'securtary@kufa.sa',
'شركة ألفا الخدمات للنقل' => 'hr@alpha-logistics.com.sa',
'مصنع احمد ابو لسه للاسفنج' => 'hr@foamco.com.sa',
'شركة اليغانسيا العربية التجارية' => 'm.mikati@eleganciagroup.com',
'ابناء صالح عبدالرحمن الرميح للتجارة' => 'f.a.hamadah@hotmail.com',
'جي اتش للنقليات' => 'legin@ghxpress.com',
'رندة عزات ابراهيم ريدان للمقاولات' => 'Alaa.dandashi@gmail.com',
'مصنع الفا للأحبار ومواد الطباعة الصناعية' => 'accounting@alphafactory.sa',
'شركة محمد صالح باحارث' => 'aaa@msbco.com',
'شركة فتحي عبدالجواد المحتسب واخوانه للتجارة والمقاولات' => 'nhazmi12@gmail.com',
'شركة ورقة ورد للتجارة' => 'info@waraqahward.com',
'مصنع بوليمرز المتقدمة للصناعة' => 'a.alkatheri@apcl.me',
'شركة دوائر السرعة للخدمات اللوجستية' => 'HR@fastcircles.com',
'مركز فيصل احمد المحمدي لصيانة السيارات' => 'ahmedf.almohammadi@gmail.com',
'سيف مارت' => '7e7en.mart@gmail.com',
'شركة الشخص التجارية' => 'samer@alshakhstrading.com',
'شركة الرابح للتغليف' => 'Mishal@alrabehpack.com',
'شركة دوائر السرعة المحدودة' => 'HR@fastcircles.com',
'شركة التشغيل المتقدم للاستثمار' => 'hr@advancedoperating.com.sa',
'شركة انظمة التتبع لتقنية المعلومات	' => 'aud@traking-systems.com',
'مصنع العمران للمطابخ المعدنية' => 'm.alanzi@alomranco.com',
'شركة فريمكس للاستثمار المحدودة' => 'Fahad.Alghareeb@frimex.com',
'مؤسسة حلول آدما للمقاولات' => 'info@admaqa.com',
'اعمال المجد للصناعة' => 'nura@backcare.com.sa',
'مصنع الحرفي الماهر للصناعات الخشبية' => 'admin@artisana1.com',
'مصنع شركة أوبال المتحدة للمنتجات الخرسانية' => 'abdullah@opal-co.com',
'مصنع الرؤيا المتقدمة للوجبات السريعة' => 'fadisamba@gmail.com',
'مصنع شركة فيدا الصناعية' => 'afalothman1991@gmail.com',
'شركة مطاعم ومطابخ السد المحدودة' => 'sulaiman@sadaresturant.com',
'شركة جرين الحديث للبلاستيك' => 'm.babkeer@filemban.com',
'ورشة الإتجاهات الستة لصيانة المعدات' => 'shalabi1953@gmail.com',
'رؤيتنا العصرية للتجارة' => 'Marwan@alkhaiyat.com',
'شركة كي أي في تيكنكل تريدنق ذ م م' => 'hr@kavalani.com',
'مصنع علي بن محمد بن ابو بكر جيلاني للمنتجات الورقية' => 'JILANIACCOUNT@yahoo.com',
        ];

        foreach ($names as $name => $email) {
            $company = Company::where('name_ar', $name)->first();

            if (!$company) {
                $this->info('Company not found: '.$name);
                continue;
            }

            $company->email = $email;
            $company->save();
            $this->info('Company updated: '.$name);
        }

        return 1;

        $companies = Company::whereNotNull('is_ptc_net')
            ->get();

        if ($this->option('company_id')) {
            $companies = Company::whereNotNull('is_ptc_net')
                ->where('id', $this->option('company_id'))
                ->get();
        }

        foreach ($companies as $company) {
            if ($company->trainees()->count() === 0) {
                continue;
            }

            // $this->info('Processing company: '.$company->name_ar.' - '.$company->id);

            // Has report for current month?
            $currentMonthReport = $company->company_attendance_reports()
                ->whereBetween('date_from', ['2023-04-01', '2023-04-30'])
                ->first();

            if ($currentMonthReport) {
                // $this->info('Current report already exists for the same period, skipping');
                continue;
            }

            $lastReport = $company->company_attendance_reports()
                ->orderBy('date_from', 'desc')
                ->first();

            if ($lastReport && $lastReport->to_emails) {
                // Is the number of trainees equal to the number of trainees in the company?
                //if ($lastReport->trainees()->count() !== $company->trainees()->count()) {
                //    $this->info('Number of trainees in the last report is not equal to the number of trainees in the company. Skipping: '.$company->name_ar);
                //    continue;
                //}

                // Are the trainees matching the IDs of the all the trainees in the company?
                //foreach ($lastReport->trainees as $trainee) {
                //    if (! $company->trainees()->where('id', $trainee->id)->first()) {
                //        $this->info('Trainee not found in the company. Skipping: '.$company->name_ar);
                //        continue 2;
                //    }
                //}

                $clone = app()->make(CompanyAttendanceReportService::class)->clone($lastReport->id);
                $clone->date_from = '2023-04-01';
                $clone->date_to = '2023-04-30';
                $clone->cc_emails = Str::replace('ptc-ksa.com', 'ptc-ksa.net', $clone->cc_emails);
                $clone->save();
                app()->make(CompanyAttendanceReportService::class)->approve($clone->id);
                $this->info('Sent for with email: '.$company->name_ar);
            } else {
                if (!$company->email) {
                    $this->info('No email for company. Skipping: '.$company->name_ar);
                    continue;
                }
                $this->info('No last report. Creating new report - '.$company->name_ar);
                $report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
                $report->date_from = '2023-04-01';
                $report->date_to = '2023-04-30';
                $report->cc_emails = 'sara@ptc-ksa.net, m_shehatah@ptc-ksa.net, ceo@ptc-ksa.net, mashael.a@ptc-ksa.net';
                $report->save();
                app()->make(CompanyAttendanceReportService::class)->approve($report->id);
                $this->info('Sent for: '.$company->name_ar);
            }
        }

        return 1;
    }
}
