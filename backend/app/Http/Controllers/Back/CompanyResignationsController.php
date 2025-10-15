<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Mail\ResignationsMail;
use App\Models\AppSetting;
use App\Models\Back\Company;
use App\Models\Back\MaxNumber;
use App\Models\Back\Resignation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Mail;

class CompanyResignationsController extends Controller
{
    /**
     *
     *
     * @param $compay_id
     * @return \Inertia\Response
     */
    public function create($compay_id)
    {
        // Get default emails from app settings
        $defaultCcEmails = AppSetting::where('name', 'resignation_default_cc_emails')->value('value') ?? '';
        $defaultBccEmails = AppSetting::where('name', 'resignation_default_bcc_emails')->value('value') ?? '';

        return Inertia::render('Back/CompanyResignations/Create', [
            'company' => Company::with('trainees')->with('resignations')->findOrFail($compay_id),
            'default_cc_emails' => $defaultCcEmails,
            'default_bcc_emails' => $defaultBccEmails,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            //'company_id' => 'required|exists:companies,id',
            //'trainees.*.id' => 'required|exists:trainees,id',
            'date' => 'required|date',
            'resignation_date' => 'nullable|date',
            //'reason' => 'required|string',
            //'emails_to' => 'required|string',
            //'emails_cc' => 'nullable|string',
            //'emails_bcc' => 'nullable|string',
        ]);

        DB::beginTransaction();


        $resignation = Resignation::create([
            'date' => Carbon::parse($request->date),
            'resignation_date' => Carbon::parse($request->resignation_date),
            'company_id' => $request->company_id,
            'reason' => $request->reason,
            'emails_to' => $request->emails_to,
            'emails_cc' => $request->emails_cc ?? [],
            'emails_bcc' => $request->emails_bcc ?? [],
        ]);


        foreach ($request->trainees as $trainee) {
            $resignation->trainees()->attach($trainee['id'], [
                'id' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
                'team_id' => $resignation->team_id,
            ]);
        }

        DB::commit();

        return redirect()->route('back.companies.show', $resignation->company_id);
    }

    public function upload($company_id, $id)
    {
        $resignation = Resignation::with('media')->with('trainees')->findOrFail($id);

        return Inertia::render('Back/CompanyResignations/Upload', [
            'company' => Company::with('trainees')->findOrFail($company_id),
            'resignation' => $resignation,
        ]);
    }

    public function uploadStore($company_id, $id, Request $request)
    {
        try {
            $request->validate([
                'resignation_file' => 'required|file|mimes:pdf|max:512000', // 500MB in KB
            ], [
                'resignation_file.required' => 'يجب اختيار ملف الاستقالة',
                'resignation_file.file' => 'يجب أن يكون الملف صالحاً',
                'resignation_file.mimes' => 'يجب أن يكون الملف من نوع PDF',
                'resignation_file.max' => 'حجم الملف يجب أن يكون أقل من 500 ميجابايت',
            ]);

            $resignation = Resignation::with('trainees')
                ->findOrFail($id);

            $resignation->media()->forceDelete();

            if ($request->file('resignation_file', [])) {
                $resignation->uploadToFolder($request->file('resignation_file'), 'resignation_files');
            }

            return redirect()->route('back.companies.show', $resignation->company_id)
                ->with('success', 'تم رفع ملف الاستقالة بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function approve($company_id, $resignation_id)
    {
        DB::beginTransaction();

        $resignation = Resignation::with('trainees')->find($resignation_id);

        if (!$resignation->has_file) {
            abort('403', 'لا يوجد ملف مرفق');
        }

        $resignation->update([
            'number' => MaxNumber::generateForPrefix('RSG', 1000),
            'status' => 'sent',
            'approved_at' => now(),
            'sent_at' => now(),
        ]);

        foreach ($resignation->trainees as $trainee) {
            $trainee->deleted_remark = $resignation->reason;
            $trainee->deleted_by_id = auth()->user()->id;
            $trainee->save();
            $trainee->delete();
        }

        // معالجة TO emails
        $toEmails = [];
        if (!empty($resignation->emails_to)) {
            $toEmails = array_filter(array_map('trim', explode(',', $resignation->emails_to)), function($email) {
                $email = trim($email);
                return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
            });
        }

        // معالجة CC emails
        $ccEmails = [];
        if (!empty($resignation->emails_cc)) {
            $ccEmails = array_filter(array_map('trim', explode(',', $resignation->emails_cc)), function($email) {
                $email = trim($email);
                return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
            });
        }

        // معالجة BCC emails
        $bccEmails = [];
        if (!empty($resignation->emails_bcc)) {
            $bccEmails = array_filter(array_map('trim', explode(',', $resignation->emails_bcc)), function($email) {
                $email = trim($email);
                return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
            });
        }

        // التأكد من وجود مستلمين
        $totalRecipients = count($toEmails) + count($ccEmails) + count($bccEmails);
        
        if ($totalRecipients === 0) {
            \Log::error('No recipients found for resignation email', [
                'resignation_id' => $resignation_id,
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
        
        // إضافة CC emails
        if (!empty($ccEmails)) {
            foreach ($ccEmails as $ccEmail) {
                $mailInstance->cc($ccEmail);
            }
        }
        
        // إضافة BCC emails
        if (!empty($bccEmails)) {
            foreach ($bccEmails as $bccEmail) {
                $mailInstance->bcc($bccEmail);
            }
        }

        // تسجيل تفاصيل الإرسال
        \Log::info('Sending resignation email', [
            'resignation_id' => $resignation_id,
            'to_count' => count($toEmails),
            'cc_count' => count($ccEmails),
            'bcc_count' => count($bccEmails)
        ]);

        $mailInstance->send(new ResignationsMail($resignation));

        DB::commit();

        return redirect()->route('back.companies.show', $resignation->company_id);
    }

    public function destroy($company_id, $resignation_id)
    {
        $resignation = Resignation::with('trainees')->find($resignation_id);
        $resignation->delete();
        return redirect()->route('back.companies.show', $resignation->company_id);
    }

    public function confirmReceived($resignation_id)
    {
        $resignation = Resignation::find($resignation_id);
        $resignation->update([
            'received_at' => now(),
        ]);
        return 'شكرًا لتأكيد الاستلام'.' - '.now()->setTimezone('Asia/Riyadh');
    }
}
