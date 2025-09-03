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
                // This is a resigned and deleted trainee - set start_date and end_date to null to ensure they're treated like regular trainees
                $traineeData[$traineeId] = [
                    'active' => true,
                    'start_date' => null, // Set to null to ensure correct work days calculation
                    'end_date' => null, // Set to null to ensure correct work days calculation
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
        
        \Log::info('Created new report ' . $report->id . ' with ' . count($traineeData) . ' trainees');
        
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
                // This is a resigned and deleted trainee - set start_date and end_date to null to ensure they're treated like regular trainees
                $traineeData[$traineeId] = [
                    'active' => true,
                    'start_date' => null, // Set to null to ensure correct work days calculation
                    'end_date' => null, // Set to null to ensure correct work days calculation
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

        Mail::to($report->emails_to()->pluck('email') ?: null)
            ->bcc($report->emails_cc()->pluck('email') ?: null)
            ->bcc($report->emails_bcc()->pluck('email') ?: null)
            ->send(new CompanyAttendanceReportMail($report->id));

        return $report;
    }

    static public function makePdf($id)
    {
        try {
            $report = CompanyAttendanceReport::findOrFail($id);

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
                '077e3421-a623-49f4-b3f2-dcf80c9d295f', // الشركة الجديدة المضافة
            ])) {
                // Use simplified design to avoid SSL issues
                $view = 'pdf.company-attendance-report.special-company-simple';
            } else {
                $view = $report->activeTraineesCount() > 8 ? 'pdf.company-attendance-report.show' : 'pdf.company-attendance-report.one-table';
            }

            // Get active trainees with proper error handling
            $active_trainees = collect();
            try {
                $active_trainees = $report->getAllTraineesWithResignations();
                \Log::info('Successfully got ' . $active_trainees->count() . ' trainees for report ' . $id);
            } catch (\Exception $e) {
                \Log::error('Error getting trainees for report ' . $id . ': ' . $e->getMessage());
                // Fallback to basic trainees if there's an error
                $active_trainees = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $id)
                    ->where('active', true)
                    ->with(['trainee' => function($q) {
                        $q->withTrashed();
                    }])->get()
                    ->filter(function($record) {
                        return $record->trainee !== null;
                    });
                \Log::info('Fallback: Got ' . $active_trainees->count() . ' trainees for report ' . $id);
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
                ->setOption('footer-html', $report->with_logo ? resource_path('views/pdf/company-attendance-report/company-attendance-report-footer.html') : false)
                ->loadView($view, [
                    'base64logo' => $report->company->logo_files->count() ? 'data:image/jpeg;base64,'.base64_encode(@file_get_contents('https://prod.jisr-ksa.com/back/media/'.$report->company->logo_files->first()->id)) : null,
                    'report' => $report,
                    'active_trainees' => $active_trainees,
                    'days' => $days,
                ]);

            return $pdf;
        } catch (\Exception $e) {
            \Log::error('Error generating PDF for report ' . $id . ': ' . $e->getMessage());
            throw new \Exception('حدث خطأ في إنشاء التقرير: ' . $e->getMessage());
        }
    }

    public static function makeIndividualPdf($report_id, $trainee_id, bool $with_attendance_times)
    {
        try {
            $record = CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $report_id)
                ->where('trainee_id', $trainee_id)
                ->with(['trainee' => function($q) {
                    $q->withTrashed();
                }, 'report'])
                ->first();
                
            if (!$record) {
                throw new \Exception('السجل غير موجود');
            }

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
            if (in_array($record->report->company->id, [
                '9ef83749-d1ba-44a5-82a9-f726840e02db', // مصنع هلال مشبب العتيبي
                '92d30511-77a8-4290-8d20-419f93ede3fd', // الشركة الجديدة
                '19762266-e0fc-43e5-b6ae-b4deec886bb1',
                '73017d20-40c8-401f-8dc1-b36ca0416e35', 
                '077e3421-a623-49f4-b3f2-dcf80c9d295f',// الشركة الجديدة المضافة
            ])) {
                // Use simplified design to avoid SSL issues
                $view = 'pdf.company-attendance-report.special-company-individual-simple';
            } else {
                $view = 'pdf.company-attendance-report.individual-table';
            }

            // Get active trainees with proper error handling
            $active_trainees = collect();
            try {
                $active_trainees = $record->report->getAllTraineesWithResignations()->where('trainee.id', $trainee_id);
            } catch (\Exception $e) {
                \Log::error('Error getting trainees for individual report ' . $report_id . ' trainee ' . $trainee_id . ': ' . $e->getMessage());
                // Fallback to basic trainee if there's an error
                $active_trainees = collect([$record]);
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
                    'base64logo' => $record->report->company->logo_files->count() ? 'data:image/jpeg;base64,'.base64_encode(@file_get_contents('https://prod.ptc-ksa.com/back/media/'.$record->report->company->logo_files->first()->id)) : null,
                    'report' => $record->report,
                    'active_trainees' => $active_trainees,
                    'days' => $days,
                    'with_attendance_times' => $with_attendance_times,
                ]);

            return $pdf;
        } catch (\Exception $e) {
            \Log::error('Error generating individual PDF for report ' . $report_id . ' trainee ' . $trainee_id . ': ' . $e->getMessage());
            throw new \Exception('حدث خطأ في إنشاء التقرير الفردي: ' . $e->getMessage());
        }
    }
}
