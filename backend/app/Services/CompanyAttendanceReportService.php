<?php

namespace App\Services;

use App\Mail\CompanyAttendanceReportMail;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyAttendanceReportsEmail;
use App\Models\Back\CompanyAttendanceReportsTrainee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mail;
use PDF;
use Str;

class CompanyAttendanceReportService
{
    public function newReport($company_id)
    {
        DB::beginTransaction();
        $report = new CompanyAttendanceReport();
        $report->company_id = $company_id;
        $report->status = CompanyAttendanceReport::STATUS_REVIEW;
        $report->date_from = Carbon::now()->startOfMonth();
        $report->date_to = Carbon::now()->endOfMonth();
        $report->with_logo = false;
        $report->save();
        $report = $report->refresh();

        // Get all trainees including deleted ones and those with resignations
        $allTrainees = collect();
        $company = $report->company;
        
        // 1. Active trainees (not deleted)
        $activeTrainees = $company->trainees()->get();
        $allTrainees = $allTrainees->merge($activeTrainees);
        
        // 2. Trainees with resignations AND deleted (soft deleted) - ONLY THESE SHOULD BE INCLUDED
        $resignationTrainees = $company->resignations()
            ->whereIn('status', ['new', 'sent']) // Include both new and sent resignations
            ->where('resignation_date', '>=', $report->date_from) // Resignation date should be within or after report period
            ->with(['trainees' => function($q) {
                $q->onlyTrashed(); // ONLY deleted trainees
            }])
            ->get()
            ->flatMap(function($resignation) {
                return $resignation->trainees;
            });
        
        $allTrainees = $allTrainees->merge($resignationTrainees);
        
        // Remove duplicates and attach to report
        $uniqueTraineeIds = $allTrainees->unique('id')->pluck('id');
        
        // Prepare trainee data with start_date and end_date for resigned trainees
        $traineeData = [];
        foreach ($uniqueTraineeIds as $traineeId) {
            $trainee = $allTrainees->firstWhere('id', $traineeId);
            
            // Check if this trainee has a resignation
            $resignation = $company->resignations()
                ->whereIn('status', ['new', 'sent'])
                ->whereHas('trainees', function($q) use ($traineeId) {
                    $q->where('trainees.id', $traineeId); // Specify table name to avoid ambiguity
                })
                ->first();
            
            if ($resignation && $trainee->trashed()) {
                // This is a resigned and deleted trainee
                $traineeData[$traineeId] = [
                    'active' => true,
                    'start_date' => $report->date_from, // Start from report start date
                    'end_date' => Carbon::parse($resignation->resignation_date)->endOfDay(), // End at resignation date
                ];
            } else {
                // This is an active trainee - set start_date and end_date to null
                $traineeData[$traineeId] = [
                    'active' => true,
                    'start_date' => null,
                    'end_date' => null,
                ];
            }
        }
        
        $report->trainees()->attach($traineeData);
        
        DB::commit();

        return $report;
    }

    public function clone($id)
    {
        DB::beginTransaction();
        $original = CompanyAttendanceReport::findOrFail($id);
        $clone = $original->replicate(['approved_by_id', 'approved_at']);
        $clone->status = CompanyAttendanceReport::STATUS_REVIEW;
        $clone->date_from = $original->date_from->clone()->addMonth();
        $clone->with_logo = false;
        if ($clone->date_from->daysInMonth < $original->date_from->daysInMonth) {
            $clone->date_to = $original->date_from
                ->clone()
                ->endOfMonth()
                ->addDay()
                ->endOfMonth();
        } else {
            $clone->date_to = $original->date_to->clone()->addMonth();
        }
        $clone->save();
        $clone = $clone->refresh();

        $clone->emails()->createMany($original->emails()->get()->map(function ($email) {
                return [
                    'type' => $email->type,
                    'email' => Str::replace(' ', '', $email->email),
                ];
            })->toArray());

        // Get all trainees including deleted ones and those with resignations for the new period
        $allTrainees = collect();
        $company = $clone->company;
        
        // 1. Active trainees (not deleted)
        $activeTrainees = $company->trainees()->get();
        $allTrainees = $allTrainees->merge($activeTrainees);
        
        // 2. Trainees with resignations AND deleted (soft deleted) - ONLY THESE SHOULD BE INCLUDED
        $resignationTrainees = $company->resignations()
            ->whereIn('status', ['new', 'sent']) // Include both new and sent resignations
            ->where('resignation_date', '>=', $clone->date_from) // Resignation date should be within or after report period
            ->with(['trainees' => function($q) {
                $q->onlyTrashed(); // ONLY deleted trainees
            }])
            ->get()
            ->flatMap(function($resignation) {
                return $resignation->trainees;
            });
        
        $allTrainees = $allTrainees->merge($resignationTrainees);
        
        // Remove duplicates and attach to report
        $uniqueTraineeIds = $allTrainees->unique('id')->pluck('id');
        
        // Get settings from original report to preserve user preferences
        $originalTraineesSettings = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $original->id)
            ->get()
            ->keyBy('trainee_id');
        
        \Log::info('Debug - Clone: Preserving settings from original report:', [
            'original_report_id' => $original->id,
            'clone_report_id' => $clone->id,
            'original_trainees_count' => $originalTraineesSettings->count(),
            'preserved_inactive_trainees' => $originalTraineesSettings->where('active', false)->count(),
        ]);
        
        // Prepare trainee data with start_date and end_date for resigned trainees
        // AND preserve previous settings (active status, comment, etc.)
        $traineeData = [];
        foreach ($uniqueTraineeIds as $traineeId) {
            $trainee = $allTrainees->firstWhere('id', $traineeId);
            
            // Check if this trainee has a resignation
            $resignation = $company->resignations()
                ->whereIn('status', ['new', 'sent'])
                ->whereHas('trainees', function($q) use ($traineeId) {
                    $q->where('trainees.id', $traineeId); // Specify table name to avoid ambiguity
                })
                ->first();
            
            // Get previous settings for this trainee if they exist
            $previousSettings = $originalTraineesSettings->get($traineeId);
            
            if ($resignation && $trainee->trashed()) {
                // This is a resigned and deleted trainee
                $traineeData[$traineeId] = [
                    'active' => $previousSettings ? $previousSettings->active : true, // Preserve previous active status
                    'status' => $previousSettings ? $previousSettings->status : null, // Preserve previous status
                    'comment' => $previousSettings ? $previousSettings->comment : null, // Preserve previous comment
                    'start_date' => $clone->date_from, // Start from report start date
                    'end_date' => Carbon::parse($resignation->resignation_date)->endOfDay(), // End at resignation date
                ];
            } else {
                // This is an active trainee - preserve previous settings if exist
                $traineeData[$traineeId] = [
                    'active' => $previousSettings ? $previousSettings->active : true, // Preserve previous active status, default true for new trainees
                    'status' => $previousSettings ? $previousSettings->status : null, // Preserve previous status
                    'comment' => $previousSettings ? $previousSettings->comment : null, // Preserve previous comment
                    'start_date' => $previousSettings && $previousSettings->start_date ? $previousSettings->start_date : null, // Preserve custom dates if exist
                    'end_date' => $previousSettings && $previousSettings->end_date ? $previousSettings->end_date : null,
                ];
            }
        }
        
        $clone->trainees()->attach($traineeData);

        DB::commit();

        return $clone;
    }

    public function approve($id)
    {
        $report = CompanyAttendanceReport::findOrFail($id);
        abort_if($report->status === CompanyAttendanceReport::STATUS_APPROVED, 404);

        $report->status = CompanyAttendanceReport::STATUS_APPROVED;
        $report->approved_by_id = auth()->user()->id ?? '7289ed33-0250-40dd-af4c-9f3e2b09eecb';
        $report->approved_at = now();
        $report->save();

        // make sure to remove any spaces from emails
        //$contact_point = 'sara@ptc-ksa.net';
        //$contact_point_exists = false;
        foreach ($report->emails as $email) {
            $email->update([
                'email' => Str::replace(' ', '', $email->email),
            ]);
            //if ($email->email == $contact_point) {
            //    $contact_point_exists = true;
            //}
        }

        //if (!$contact_point_exists) {
        //    $report->emails()->create([
        //        'type' => 'cc',
        //        'email' => $contact_point,
        //    ]);
        //}

        // Fix emails
        foreach ($report->emails as $emailRecord) {
            if (Str::contains($emailRecord->email, ',')) {
                $splitEmails = explode(',', $emailRecord->email);
                foreach ($splitEmails as $split) {
                    $report->emails()->create([
                        'type' => $emailRecord->type,
                        'email' => $split,
                    ]);
                }
                $emailRecord->delete();
            }
        }

        CompanyAttendanceReportsEmail::where('company_attendance_report_id', $report->id)->where('email', '')->delete();
        CompanyAttendanceReportsEmail::where('company_attendance_report_id', $report->id)->where('email', 'mahmoud.m@ptc-ksa.net')->delete();

        // معالجة TO emails
        $toEmails = $report->emails_to()->pluck('email')->filter(function($email) {
            return !empty(trim($email)) && filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        })->map(function($email) {
            return trim($email);
        })->toArray();

        // معالجة CC emails
        $ccEmails = $report->emails_cc()->pluck('email')->filter(function($email) {
            return !empty(trim($email)) && filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        })->map(function($email) {
            return trim($email);
        })->toArray();

        // معالجة BCC emails
        $bccEmails = $report->emails_bcc()->pluck('email')->filter(function($email) {
            return !empty(trim($email)) && filter_var(trim($email), FILTER_VALIDATE_EMAIL);
        })->map(function($email) {
            return trim($email);
        })->toArray();

        // التأكد من وجود مستلمين
        $totalRecipients = count($toEmails) + count($ccEmails) + count($bccEmails);
        
        if ($totalRecipients === 0) {
            \Log::error('No recipients found for attendance report email (Service)', [
                'report_id' => $report->id,
                'to_emails' => $toEmails,
                'cc_emails' => $ccEmails,
                'bcc_emails' => $bccEmails
            ]);
            throw new \Exception('لا توجد عناوين بريد إلكتروني صحيحة للإرسال');
        }

        // إنشاء instance الإيميل
        $mailInstance = null;
        
        if (!empty($toEmails)) {
            $mailInstance = Mail::to($toEmails);
        } else if (!empty($ccEmails)) {
            // إذا لم تكن هناك TO emails، استخدم أول CC كـ TO
            $mailInstance = Mail::to($ccEmails[0]);
            array_shift($ccEmails);
        } else if (!empty($bccEmails)) {
            // إذا لم تكن هناك TO أو CC emails، استخدم أول BCC كـ TO
            $mailInstance = Mail::to($bccEmails[0]);
            array_shift($bccEmails);
        }
        
        // إضافة CC emails - استخدام مصفوفة واحدة
        if (!empty($ccEmails)) {
            $mailInstance->cc($ccEmails);
        }
        
        // إضافة BCC emails - استخدام مصفوفة واحدة بدلاً من loop
        if (!empty($bccEmails)) {
            $mailInstance->bcc($bccEmails);
        }

        // تسجيل تفاصيل الإرسال
        \Log::info('Sending attendance report email (Service)', [
            'report_id' => $report->id,
            'to_count' => count($toEmails),
            'cc_count' => count($ccEmails),
            'bcc_count' => count($bccEmails)
        ]);

        $mailInstance->send(new CompanyAttendanceReportMail($report->id));

        return $report;
    }

    static public function makePdf($id)
    {
        $report = CompanyAttendanceReport::with(['company', 'trainees.trainee'])->findOrFail($id);
        
        // التحقق من وجود البيانات المطلوبة
        if (!$report || !$report->company) {
            throw new \Exception('Report or company data not found');
        }

        $days = [];
        Carbon::setLocale('ar');
        $date_from = Carbon::parse($report->date_from);
        $date_to = Carbon::parse($report->date_to);
        $i = 0;
        $current_day = $date_from;

        for ($i; $current_day->isBefore($date_to); $i++) {
            $days[] = [
                'vacation_day' => in_array($current_day->getTranslatedDayName(), ['الجمعة', 'السبت']),
                'name' => $current_day->getTranslatedDayName(),
                'date' => $current_day->format('Y-m-d'),
                'date_carbon' => $current_day->copy(),
            ];
            $current_day = $current_day->addDay();
        }

        // To fix formatting issue on 2nd page when the table is split.
        // Check if this is the special company to use different design
        if (in_array($report->company->id, [
            '9ef83749-d1ba-44a5-82a9-f726840e02db', // مصنع هلال مشبب العتيبي
            '92d30511-77a8-4290-8d20-419f93ede3fd', // الشركة الجديدة
            '19762266-e0fc-43e5-b6ae-b4deec886bb1',
            '73017d20-40c8-401f-8dc1-b36ca0416e35',
            '077e3421-a623-49f4-b3f2-dcf80c9d295f',
            'b455f112-ff48-4647-8db6-a3d365a3d0a3',
            '2d8b0e51-5ea6-4c4d-9c38-ec38429cb74e',
            '0e0e3d03-a9ad-4964-8c5a-6826cc5b0c6f',
        ])) {
            // Use simplified design to avoid SSL issues
            $view = 'pdf.company-attendance-report.special-company-simple';
        } elseif (in_array($report->company->id, [
            // شركات التصميم الحديث - يمكن إضافة معرفات الشركات هنا
            // '2ea73041-e686-4093-b830-260b488eb014', // تم إزالتها للسماح باختيار القالب حسب template_type
            // أضف معرفات الشركات الجديدة التي تريد استخدام التصميم الحديث
        ])) {
            // Use modern design template
            $view = 'pdf.company-attendance-report.special-company-modern';
        } else {
            // Use template based on user selection or default
            $templateType = $report->template_type;
            if (empty($templateType) || $templateType === '') {
                $templateType = 'default';
            }
            switch ($templateType) {
                case 'simple':
                    $view = 'pdf.company-attendance-report.special-company-simple';
                    break;
                case 'modern':
                    $view = 'pdf.company-attendance-report.special-company-modern';
                    break;
                case 'gradient':
                    $view = 'pdf.company-attendance-report.special-company-gradient';
                    break;
                default:
                    $view = $report->activeTraineesCount() > 8 ? 'pdf.company-attendance-report.show' : 'pdf.company-attendance-report.one-table';
                    break;
            }
        }

        //if ($report->company->logo_files->count()) {
        //    $tmpfname = tempnam("/tmp", "UL_IMAGE");
        //    $img = file_get_contents('https://prod.ptc-ksa.net/back/media/'.$report->company->logo_files->first()->id);
        //    file_put_contents($tmpfname, $img);
        //}


        $pdf = PDF::setOption('margin-bottom', 30)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape')
            ->setOption('encoding','utf-8')
            ->setOption('dpi', 300)
            ->setOption('image-dpi', 300)
            ->setOption('lowquality', false)
            ->setOption('no-background', false)
            ->setOption('enable-internal-links', true)
            ->setOption('enable-external-links', true)
            ->setOption('javascript-delay', 1000)
            ->setOption('no-stop-slow-scripts', true)
            ->setOption('no-background', false)
            ->setOption('margin-left', 10)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 30)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('viewport-size', '1024×768')
            ->setOption('zoom', 0.78)
            ->setOption('footer-html', $report->with_logo ? resource_path('views/pdf/company-attendance-report/company-attendance-report-footer.html') : false);
        
        $activeTrainees = $report->getAllTraineesWithResignations();
        
        // Get base64 logo with SSL context
        $base64logo = null;
        if ($report->company->logo_files->count()) {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
            $logoContent = @file_get_contents('https://prod.jisr-ksa.com/back/media/'.$report->company->logo_files->first()->id, false, $context);
            if ($logoContent) {
                $base64logo = 'data:image/jpeg;base64,'.base64_encode($logoContent);
            }
        }
        
        $pdf = $pdf->loadView($view, [
                'base64logo' => $base64logo,
                'report' => $report,
                'active_trainees' => $activeTrainees,
                'days' => $days,
            ]);

        return $pdf;
    }

    public static function makeIndividualPdf($report_id, $trainee_id, bool $with_attendance_times)
    {
        $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $report_id)
            ->where('trainee_id', $trainee_id)
            ->with('trainee', 'report')
            ->first();

        $days = [];
        Carbon::setLocale('ar');
        $date_from = Carbon::parse($record->report->date_from);
        $date_to = Carbon::parse($record->report->date_to);
        $i = 0;
        $current_day = $date_from;

        for ($i; $current_day->isBefore($date_to); $i++) {
            $days[] = [
                'vacation_day' => in_array($current_day->getTranslatedDayName(), ['الجمعة', 'السبت']),
                'name' => $current_day->getTranslatedDayName(),
                'date' => $current_day->format('Y-m-d'),
                'date_carbon' => $current_day->copy(),
            ];
            $current_day = $current_day->addDay();
        }

        // Check if this is the special company to use different design
        if (in_array($record->company->id, [
            '9ef83749-d1ba-44a5-82a9-f726840e02db', // مصنع هلال مشبب العتيبي
            '92d30511-77a8-4290-8d20-419f93ede3fd', // الشركة الجديدة
            '19762266-e0fc-43e5-b6ae-b4deec886bb1',
            '73017d20-40c8-401f-8dc1-b36ca0416e35',
            '077e3421-a623-49f4-b3f2-dcf80c9d295f', 
            'b455f112-ff48-4647-8db6-a3d365a3d0a3',
            '2d8b0e51-5ea6-4c4d-9c38-ec38429cb74e',
            '0e0e3d03-a9ad-4964-8c5a-6826cc5b0c6f',
        ])) {
            // Use simplified design to avoid SSL issues
            $view = 'pdf.company-attendance-report.special-company-individual-simple';
        } elseif (in_array($record->company->id, [
            // شركات التصميم الحديث - يمكن إضافة معرفات الشركات هنا
            // '2ea73041-e686-4093-b830-260b488eb014', // تم إزالتها للسماح باختيار القالب حسب template_type
            // أضف معرفات الشركات الجديدة التي تريد استخدام التصميم الحديث
        ])) {
            // Use modern design template for individual reports
            $view = 'pdf.company-attendance-report.special-company-individual-modern';
        } else {
            // Use template based on user selection or default
            $templateType = $record->report->template_type;
            if (empty($templateType) || $templateType === '') {
                $templateType = 'default';
            }
            switch ($templateType) {
                case 'simple':
                    $view = 'pdf.company-attendance-report.special-company-individual-simple';
                    break;
                case 'modern':
                    $view = 'pdf.company-attendance-report.special-company-individual-modern';
                    break;
                case 'gradient':
                    $view = 'pdf.company-attendance-report.special-company-individual-gradient';
                    break;
                default:
                    $view = 'pdf.company-attendance-report.individual-table';
                    break;
            }
        }

        // Get base64 logo with SSL context
        $base64logo = null;
        if ($record->report->company->logo_files->count()) {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
            $logoContent = @file_get_contents('https://prod.ptc-ksa.com/back/media/'.$record->report->company->logo_files->first()->id, false, $context);
            if ($logoContent) {
                $base64logo = 'data:image/jpeg;base64,'.base64_encode($logoContent);
            }
        }
        
        $pdf = PDF::setOption('margin-bottom', 30)
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'landscape')
            ->setOption('encoding','utf-8')
            ->setOption('dpi', 300)
            ->setOption('image-dpi', 300)
            ->setOption('lowquality', false)
            ->setOption('no-background', false)
            ->setOption('enable-internal-links', true)
            ->setOption('enable-external-links', true)
            ->setOption('javascript-delay', 1000)
            ->setOption('no-stop-slow-scripts', true)
            ->setOption('no-background', false)
            ->setOption('margin-left', 10)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 30)
            ->setOption('disable-smart-shrinking', true)
            ->setOption('viewport-size', '1024×768')
            ->setOption('zoom', 0.78)
            ->setOption('footer-html', resource_path('views/pdf/company-attendance-report/company-attendance-report-footer.html'))
            ->loadView($view, [
                'base64logo' => $base64logo,
                'report' => $record->report,
                'active_trainees' => $record->report->getAllTraineesWithResignations()->where('trainee.id', $trainee_id),
                'days' => $days,
                'with_attendance_times' => $with_attendance_times,
            ]);

        return $pdf;
    }
}
