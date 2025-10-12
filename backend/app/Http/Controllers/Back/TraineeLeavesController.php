<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\TraineeLeave;
use App\Models\Back\Trainee;
use App\Models\Back\Company;
use App\Mail\MaternityLeaveMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class TraineeLeavesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($trainee_id)
    {
        try {
            $trainee = Trainee::findOrFail($trainee_id);
            $leaves = TraineeLeave::where('trainee_id', $trainee_id)
                ->with('trainee')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($leaves);
        } catch (\Exception $e) {
            return response()->json(['error' => 'حدث خطأ أثناء تحميل البيانات'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);
        
        return Inertia::render('Back/TraineeLeaves/Create', [
            'trainee' => $trainee,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $trainee_id)
    {
        $request->validate([
            'leave_type' => 'required|string|in:أجازة وضع',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'notes' => 'nullable|string',
            'leave_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'send_email' => 'nullable|boolean',
            'email_to' => 'nullable|string',
            'email_cc' => 'nullable|string',
            'email_bcc' => 'nullable|string',
        ]);

        $trainee = Trainee::findOrFail($trainee_id);

        $leave = TraineeLeave::create([
            'trainee_id' => $trainee_id,
            'leave_type' => $request->leave_type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('leave_file')) {
            $leave->addMediaFromRequest('leave_file')
                ->toMediaCollection('leave_file');
        }

        // إرسال الإيميل إذا كان مطلوباً وكان نوع الإجازة هو إجازة وضع
        if ($request->boolean('send_email') && $request->leave_type === 'أجازة وضع') {
            \Log::info('Maternity Leave Email Process Started', [
                'trainee_id' => $trainee_id,
                'trainee_name' => $trainee->name,
                'leave_id' => $leave->id,
                'company_name' => $trainee->company ? $trainee->company->name_ar : 'No company',
                'email_data' => [
                    'to' => $request->email_to,
                    'cc' => $request->email_cc,
                    'bcc' => $request->email_bcc,
                ],
                'user_id' => auth()->id(),
                'timestamp' => now()->toDateTimeString()
            ]);

            try {
                $emailSent = $this->sendMaternityLeaveEmail($trainee, $leave, [
                    'to' => $request->email_to,
                    'cc' => $request->email_cc,
                    'bcc' => $request->email_bcc,
                ]);

                if ($emailSent) {
                    \Log::info('Maternity Leave Email Sent Successfully', [
                        'trainee_id' => $trainee_id,
                        'leave_id' => $leave->id,
                        'trainee_name' => $trainee->name
                    ]);
                } else {
                    \Log::error('Maternity Leave Email Failed - No Recipients or Other Issue', [
                        'trainee_id' => $trainee_id,
                        'leave_id' => $leave->id,
                        'email_data' => [
                            'to' => $request->email_to,
                            'cc' => $request->email_cc,
                            'bcc' => $request->email_bcc,
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Maternity Leave Email Exception', [
                    'trainee_id' => $trainee_id,
                    'leave_id' => $leave->id,
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString(),
                    'email_data' => [
                        'to' => $request->email_to,
                        'cc' => $request->email_cc,
                        'bcc' => $request->email_bcc,
                    ]
                ]);
            }
        } else {
            \Log::info('Maternity Leave Email Not Sent', [
                'trainee_id' => $trainee_id,
                'reason' => 'Email not requested or leave type is not maternity',
                'send_email_requested' => $request->boolean('send_email'),
                'leave_type' => $request->leave_type
            ]);
        }

        return redirect()->route('back.trainees.show', $trainee_id)
            ->with('success', 'تم إرسال طلب الإجازة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show($trainee_id, $id)
    {
        $leave = TraineeLeave::where('trainee_id', $trainee_id)
            ->where('id', $id)
            ->with('trainee')
            ->firstOrFail();

        return Inertia::render('Back/TraineeLeaves/Show', [
            'leave' => $leave,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($trainee_id, $id)
    {
        $leave = TraineeLeave::where('trainee_id', $trainee_id)
            ->where('id', $id)
            ->with('trainee')
            ->firstOrFail();

        return Inertia::render('Back/TraineeLeaves/Edit', [
            'leave' => $leave,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $trainee_id, $id)
    {
        $request->validate([
            'leave_type' => 'required|string|in:أجازة وضع',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'notes' => 'nullable|string',
            'leave_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $leave = TraineeLeave::where('trainee_id', $trainee_id)
            ->where('id', $id)
            ->firstOrFail();

        $leave->update([
            'leave_type' => $request->leave_type,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('leave_file')) {
            // حذف الملف القديم إذا كان موجوداً
            $leave->clearMediaCollection('leave_file');
            
            // إضافة الملف الجديد
            $leave->addMediaFromRequest('leave_file')
                ->toMediaCollection('leave_file');
        }

        return response()->json(['message' => 'تم تحديث طلب الإجازة بنجاح']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($trainee_id, $id)
    {
        try {
            $leave = TraineeLeave::where('trainee_id', $trainee_id)
                ->where('id', $id)
                ->firstOrFail();

            // حذف الملفات المرتبطة
            $leave->clearMediaCollection('leave_file');
            
            // حذف الإجازة
            $leave->delete();

            return response()->json(['message' => 'تم حذف طلب الإجازة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء حذف الإجازة'], 500);
        }
    }

    /**
     * Update leave status
     */
    public function updateStatus(Request $request, $trainee_id, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $leave = TraineeLeave::where('trainee_id', $trainee_id)
            ->where('id', $id)
            ->firstOrFail();

        $leave->update(['status' => $request->status]);

        return response()->json(['message' => 'تم تحديث حالة طلب الإجازة بنجاح']);
    }

    /**
     * Get company email and default email data for maternity leave
     */
    public function getEmailDefaults($trainee_id)
    {
        \Log::info('getEmailDefaults Called', [
            'trainee_id' => $trainee_id,
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            $trainee = Trainee::with('company')->findOrFail($trainee_id);
            
            \Log::info('Trainee Found for Email Defaults', [
                'trainee_id' => $trainee->id,
                'trainee_name' => $trainee->name,
                'company_id' => $trainee->company_id,
                'company_name' => $trainee->company ? $trainee->company->name_ar : 'No company',
                'company_email' => $trainee->company ? $trainee->company->email : 'No email'
            ]);
            
            $defaultEmails = [
                'ceo@hadaf-hq.com',
                'afnan@hadaf-hq.com',
                'M_SHEHATAH@hadaf-hq.com',
                'sara@hadaf-hq.com',
                'mashael.a@hadaf-hq.com',
                'eslam@hadaf-hq.com',
                'mahmoud.h@hadaf-hq.com',
                'halim@hadaf-hq.com'
            ];

            $responseData = [
                'company_email' => $trainee->company ? $trainee->company->email : '',
                'default_bcc_emails' => implode(', ', $defaultEmails),
                'trainee_name' => $trainee->name,
                'company_name' => $trainee->company ? $trainee->company->name_ar : ''
            ];

            \Log::info('Email Defaults Prepared Successfully', [
                'trainee_id' => $trainee_id,
                'response_data' => $responseData
            ]);

            return response()->json($responseData);
        } catch (\Exception $e) {
            \Log::error('Error Getting Email Defaults', [
                'trainee_id' => $trainee_id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'فشل في تحميل بيانات البريد الإلكتروني'
            ], 500);
        }
    }

    /**
     * Send maternity leave email
     */
    private function sendMaternityLeaveEmail(Trainee $trainee, TraineeLeave $leave, array $emailData)
    {
        \Log::info('sendMaternityLeaveEmail Method Called', [
            'trainee_id' => $trainee->id,
            'trainee_name' => $trainee->name,
            'leave_id' => $leave->id,
            'email_data' => $emailData
        ]);

        try {
            // تحضير قوائم المستلمين
            $toEmails = [];
            $ccEmails = [];
            $bccEmails = [];

            // معالجة TO emails
            if (!empty($emailData['to'])) {
                $toEmails = array_filter(array_map('trim', explode(',', $emailData['to'])));
                \Log::info('TO Emails Prepared', ['emails' => $toEmails]);
            }
            
            // معالجة CC emails
            if (!empty($emailData['cc'])) {
                $ccEmails = array_filter(array_map('trim', explode(',', $emailData['cc'])));
                \Log::info('CC Emails Prepared', ['emails' => $ccEmails]);
            }
            
            // معالجة BCC emails
            if (!empty($emailData['bcc'])) {
                $bccEmails = array_filter(array_map('trim', explode(',', $emailData['bcc'])));
                \Log::info('BCC Emails Prepared', ['emails' => $bccEmails]);
            }

            // التأكد من وجود مستلمين
            $totalRecipients = count($toEmails) + count($ccEmails) + count($bccEmails);
            if ($totalRecipients === 0) {
                \Log::warning('No Recipients Found for Email', [
                    'email_data' => $emailData,
                    'to_count' => count($toEmails),
                    'cc_count' => count($ccEmails),
                    'bcc_count' => count($bccEmails)
                ]);
                return false;
            }

            \Log::info('Recipients Summary', [
                'to_count' => count($toEmails),
                'cc_count' => count($ccEmails),
                'bcc_count' => count($bccEmails),
                'total_recipients' => $totalRecipients
            ]);

            // إنشاء كائن الإيميل
            $mail = new MaternityLeaveMail($trainee, $leave, $emailData);
            \Log::info('MaternityLeaveMail Object Created');

            // إرسال الإيميل باستخدام نفس طريقة الاستقالات
            if (!empty($toEmails)) {
                // إرسال إلى TO مع CC و BCC
                $mailInstance = Mail::to($toEmails);
                
                if (!empty($ccEmails)) {
                    foreach ($ccEmails as $ccEmail) {
                        $mailInstance->cc($ccEmail);
                    }
                }
                
                if (!empty($bccEmails)) {
                    foreach ($bccEmails as $bccEmail) {
                        $mailInstance->bcc($bccEmail);
                    }
                }
                
                \Log::info('About to Send Email', [
                    'to_emails' => $toEmails,
                    'cc_emails' => $ccEmails,
                    'bcc_emails' => $bccEmails
                ]);
                
                $mailInstance->send($mail);
                
            } else if (!empty($ccEmails) || !empty($bccEmails)) {
                // إذا لم تكن هناك TO emails، استخدم أول CC أو BCC كـ TO
                $primaryEmail = !empty($ccEmails) ? array_shift($ccEmails) : array_shift($bccEmails);
                
                \Log::info('Using Primary Email as TO', ['primary_email' => $primaryEmail]);
                
                $mailInstance = Mail::to($primaryEmail);
                
                if (!empty($ccEmails)) {
                    foreach ($ccEmails as $ccEmail) {
                        $mailInstance->cc($ccEmail);
                    }
                }
                
                if (!empty($bccEmails)) {
                    foreach ($bccEmails as $bccEmail) {
                        $mailInstance->bcc($bccEmail);
                    }
                }
                
                $mailInstance->send($mail);
            }

            \Log::info('Maternity Leave Email Sent Successfully', [
                'trainee_id' => $trainee->id,
                'leave_id' => $leave->id,
                'total_recipients' => $totalRecipients
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error Sending Maternity Leave Email', [
                'trainee_id' => $trainee->id,
                'leave_id' => $leave->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'email_data' => $emailData
            ]);
            return false;
        }
    }
}

