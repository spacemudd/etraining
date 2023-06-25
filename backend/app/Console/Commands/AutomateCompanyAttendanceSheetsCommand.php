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
'شركة لامار الخليجية',
'شركة النعمان إخوان العالمية للتجارة المحدودة',
'شركة شجرة الدر لتقديم الوجبات',
'شركة الرك للمقاولات',
'شركة خالد ورعد عبدالعزيز السحيم للنقل',
'لشركة عبدالرحمن عثمان محمد العثمان وشريكه',
'ورشة كراج مرن لصيانة السيارات',
'شركة خلدون بن مالك بنارس خان وشريكه',
'شركة مصنع السمو لإنتاج مياه الشرب المحدودة',
'ورشة الإتجاهات السته لصيانة السيارات',
'شركة وضوح الشرق للحديد',
'مصنع مكيفات الراحة فرع شركة العمران للصناعة والتجارة',
'شركة النافورة لإدارة الفنادق المحدوده',
'شركة وثاق العالمية المحدودة',
'شركة مصنع محمد سالم العجيمي وشريكة المحدودة',
'مؤسسة عماد عبدالجواد المحتسب للتجارة',
'شركة كيلة الشيف لتقديم الوجبات',
'شركة الهاجري لتجارة الأغذية',
'شركة ايتاب الدولية للتجارة',
'شركة اتمار للمقاولات',
'شركة الكوفة للأغذية',
'شركة ألفا الخدمات للنقل',
'مصنع احمد ابو لسه للاسفنج',
'شركة اليغانسيا العربية التجارية',
'ابناء صالح عبدالرحمن الرميح للتجارة',
'جي اتش للنقليات',
'رندة عزات ابراهيم ريدان للمقاولات',
'مصنع الفا للأحبار ومواد الطباعة الصناعية',
'شركة محمد صالح باحارث',
'شركة فتحي عبدالجواد المحتسب واخوانه للتجارة والمقاولات',
'شركة ورقة ورد للتجارة',
'مصنع بوليمرز المتقدمة للصناعة',
'شركة دوائر السرعة للخدمات اللوجستية',
'مركز فيصل احمد المحمدي لصيانة السيارات',
'سيف مارت',
'شركة الشخص التجارية',
'شركة الرابح للتغليف',
'شركة دوائر السرعة المحدودة',
'شركة التشغيل المتقدم للاستثمار',
'شركة انظمة التتبع  لتقنية المعلومات',
'مصنع العمران للمطابخ المعدنية',
'شركة فريمكس للاستثمار المحدودة',
'مؤسسة حلول آدما للمقاولات',
'اعمال المجد للصناعة',
'مصنع الحرفي الماهر للصناعات الخشبية',
'مصنع شركة أوبال المتحدة للمنتجات الخرسانية',
'مصنع الرؤيا المتقدمة للوجبات السريعة',
'مصنع شركة فيدا الصناعية',
'شركة مطاعم ومطابخ السد المحدودة',
'شركة جرين الحديث للبلاستيك',
'ورشة الإتجاهات الستة لصيانة المعدات',
'رؤيتنا العصرية للتجارة',
'شركة كي أي في تيكنكل تريدنق ذ م م',
'مصنع علي بن محمد بن ابو بكر جيلاني للمنتجات الورقية',
        ];

        //foreach ($names as $name => $email) {
        //    $company = Company::where('name_ar', $name)->first();
        //
        //    if (!$company) {
        //        $this->info('Company not found: '.$name);
        //        continue;
        //    }
        //
        //    $company->email = $email;
        //    $company->save();
        //    $this->info('Company updated: '.$name);
        //}
        //
        //return 1;

        $companies = Company::whereNotNull('is_ptc_net')
            ->whereIn('name_ar', $names)
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

            $this->info('Processing company: '.$company->name_ar);

            // Has report for current month?
            $currentMonthReport = $company->company_attendance_reports()
                ->whereBetween('date_from', ['2023-06-01', '2023-06-31'])
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

                //$clone = app()->make(CompanyAttendanceReportService::class)->clone($lastReport->id);
                $clone = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
                $clone->date_from = '2023-06-01';
                $clone->date_to = '2023-06-30';
                $clone->cc_emails = Str::replace('ptc-ksa.com', 'ptc-ksa.net', $lastReport->cc_emails);
                $clone->cc_emails = Str::replace('mashal.a@ptc-ksa.net', 'mashael.a@ptc-ksa.net', $clone->cc_emails);
                if ($company->salesperson_email) {
                    $clone->cc_emails .= ', '.$company->salesperson_email;
                }
                $clone->save();
                app()->make(CompanyAttendanceReportService::class)->approve($clone->id);
            } else {
                if (!$company->email) {
                    $this->info('No email for company. Skipping: '.$company->name_ar);
                    continue;
                }
                $this->info('No last report. Creating new report - '.$company->name_ar);
                $report = app()->make(CompanyAttendanceReportService::class)->newReport($company->id);
                $report->date_from = '2023-06-01';
                $report->date_to = '2023-06-30';
                $report->cc_emails = 'sara@ptc-ksa.net, m_shehatah@ptc-ksa.net, ceo@ptc-ksa.net, mashael.a@ptc-ksa.net';
                if ($company->salesperson_email) {
                    $report->cc_emails .= ', '.$company->salesperson_email;
                }
                $report->save();
                app()->make(CompanyAttendanceReportService::class)->approve($report->id);
            }
        }

        return 1;
    }
}
