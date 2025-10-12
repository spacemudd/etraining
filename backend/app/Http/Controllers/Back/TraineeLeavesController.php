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
            $this->sendMaternityLeaveEmail($trainee, $leave, [
                'to' => $request->email_to,
                'cc' => $request->email_cc,
                'bcc' => $request->email_bcc,
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
        $trainee = Trainee::with('company')->findOrFail($trainee_id);
        
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

        return response()->json([
            'company_email' => $trainee->company ? $trainee->company->email : '',
            'default_bcc_emails' => implode(', ', $defaultEmails),
            'trainee_name' => $trainee->name,
            'company_name' => $trainee->company ? $trainee->company->name_ar : ''
        ]);
    }

    /**
     * Send maternity leave email
     */
    private function sendMaternityLeaveEmail(Trainee $trainee, TraineeLeave $leave, array $emailData)
    {
        try {
            // التأكد من وجود مستلمين
            if (empty($emailData['to']) && empty($emailData['cc']) && empty($emailData['bcc'])) {
                return false;
            }

            $mail = new MaternityLeaveMail($trainee, $leave, $emailData);
            
            // إعداد المستلمين
            $mailBuilder = Mail::to([]);
            
            if (!empty($emailData['to'])) {
                $toEmails = array_filter(array_map('trim', explode(',', $emailData['to'])));
                $mailBuilder = Mail::to($toEmails);
            }
            
            if (!empty($emailData['cc'])) {
                $ccEmails = array_filter(array_map('trim', explode(',', $emailData['cc'])));
                foreach ($ccEmails as $ccEmail) {
                    $mailBuilder->cc($ccEmail);
                }
            }
            
            if (!empty($emailData['bcc'])) {
                $bccEmails = array_filter(array_map('trim', explode(',', $emailData['bcc'])));
                foreach ($bccEmails as $bccEmail) {
                    $mailBuilder->bcc($bccEmail);
                }
            }
            
            $mailBuilder->send($mail);
            return true;
        } catch (\Exception $e) {
            // يمكن إضافة تسجيل الأخطاء هنا
            \Log::error('Error sending maternity leave email: ' . $e->getMessage());
            return false;
        }
    }
}

