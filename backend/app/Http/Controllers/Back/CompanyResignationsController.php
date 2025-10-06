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
            \Log::info('Upload request received', [
                'company_id' => $company_id,
                'resignation_id' => $id,
                'has_file' => $request->hasFile('resignation_file'),
                'all_input' => $request->all(),
                'files' => $request->files->all(),
            ]);

            // التحقق من وجود الملف أولاً
            if (!$request->hasFile('resignation_file')) {
                \Log::error('No file provided in request');
                return redirect()->back()
                    ->with('error', 'لم يتم اختيار أي ملف للرفع')
                    ->withInput();
            }

            $file = $request->file('resignation_file');
            
            // التحقق من صحة الملف
            if (!$file || !$file->isValid()) {
                \Log::error('Invalid file provided', [
                    'file_valid' => $file ? $file->isValid() : 'No file',
                    'file_error' => $file ? $file->getError() : 'No file',
                ]);
                return redirect()->back()
                    ->with('error', 'الملف المرفق غير صالح أو تالف')
                    ->withInput();
            }

            // تسجيل معلومات الطلب
            \Log::info('Resignation file upload attempt', [
                'company_id' => $company_id,
                'resignation_id' => $id,
                'file_size' => $file->getSize(),
                'file_name' => $file->getClientOriginalName(),
                'file_mime' => $file->getMimeType(),
                'file_path' => $file->getPathname(),
                'file_exists' => file_exists($file->getPathname()),
            ]);

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

            \Log::info('Resignation found', ['resignation_id' => $resignation->id]);

            // حذف الملفات السابقة
            $resignation->media()->forceDelete();
            \Log::info('Previous media deleted');

            // التحقق مرة أخرى من وجود الملف قبل الرفع
            if (!$file->isValid() || !file_exists($file->getPathname())) {
                \Log::error('File became invalid before upload', [
                    'file_valid' => $file->isValid(),
                    'file_exists' => file_exists($file->getPathname()),
                    'file_size' => $file->getSize(),
                ]);
                return redirect()->back()
                    ->with('error', 'الملف لم يعد صالحاً للرفع')
                    ->withInput();
            }

            \Log::info('Starting file upload to folder');
            $media = $resignation->uploadToFolder($file, 'resignation_files');
            \Log::info('File uploaded successfully', ['media_id' => $media->id]);

            return redirect()->route('back.companies.show', $resignation->company_id)
                ->with('success', 'تم رفع ملف الاستقالة بنجاح');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error during file upload', [
                'errors' => $e->validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Exception during file upload', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
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

        // explode emails to array with comma separated and then join both emails_cc and emails_cc
        $emails_cc = $resignation->emails_cc ? explode(', ', $resignation->emails_cc) : null;
        $emails_bcc = $resignation->emails_bcc ? explode(', ', $resignation->emails_bcc) : null;

        // join emails_cc and emails_cc arrays
        $emails = array_merge($emails_cc, $emails_bcc);

        Mail::to($resignation->emails_to ? explode(', ', $resignation->emails_to) : null)
            ->bcc($emails)
            ->send(new ResignationsMail($resignation));

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
