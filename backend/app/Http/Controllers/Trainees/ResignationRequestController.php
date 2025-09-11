<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Mail\TraineeResignationRequestMail;
use App\Models\Back\Trainee;
use App\Models\TraineeResignationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ResignationRequestController extends Controller
{
    public function index()
    {
        $trainee = auth()->user()->trainee;
        
        // التحقق من وجود طلب استقالة سابق
        $existingRequest = TraineeResignationRequest::where('trainee_id', $trainee->id)->first();
        
        return Inertia::render('Trainees/ResignationRequest/Index', [
            'trainee' => [
                'id' => $trainee->id,
                'name' => $trainee->name,
                'identity_number' => $trainee->identity_number,
                'phone' => $trainee->phone,
                'email' => auth()->user()->email,
            ],
            'existingRequest' => $existingRequest ? [
                'id' => $existingRequest->id,
                'status' => $existingRequest->status,
                'status_text' => $existingRequest->status_text,
                'contact_phone' => $existingRequest->contact_phone,
                'created_at' => $existingRequest->created_at->format('Y-m-d H:i:s'),
                'processed_at' => $existingRequest->processed_at?->format('Y-m-d H:i:s'),
                'admin_notes' => $existingRequest->admin_notes,
            ] : null
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'contact_phone' => 'nullable|string',
                'confirmation' => 'required|boolean|accepted'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }

        $trainee = auth()->user()->trainee;

        // التحقق من وجود طلب سابق
        $existingRequest = TraineeResignationRequest::where('trainee_id', $trainee->id)->first();
        if ($existingRequest) {
            return response()->json([
                'error' => 'لديك طلب استقالة سابق. يرجى الانتظار حتى يتم معالجة طلبك الحالي.'
            ], 422);
        }

        // إنشاء طلب استقالة جديد
        $resignationRequest = TraineeResignationRequest::create([
            'trainee_id' => $trainee->id,
            'contact_phone' => $request->contact_phone,
            'status' => 'pending'
        ]);

        // إرسال الإيميل
        try {
            // استخدام نفس طريقة إرسال الإيميلات في النظام
            Mail::to(['mashael.a@hadaf-hq.com', 'ebrahim.hosny@hadaf-hq.com'])->send(
                new TraineeResignationRequestMail($trainee, $request->contact_phone)
            );
            
            Log::info('Resignation request email sent successfully', [
                'trainee_id' => $trainee->id,
                'trainee_name' => $trainee->name,
                'contact_phone' => $request->contact_phone
            ]);
            
            return response()->json([
                'success' => 'تم إرسال طلب الاستقالة بنجاح. سيتم التواصل معك قريباً.',
                'message' => 'تم إرسال طلب الاستقالة بنجاح. سيتم التواصل معك قريباً.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send resignation request email', [
                'trainee_id' => $trainee->id,
                'trainee_name' => $trainee->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // حتى لو فشل الإيميل، الطلب محفوظ في قاعدة البيانات
            return response()->json([
                'success' => 'تم إرسال طلب الاستقالة بنجاح. سيتم التواصل معك قريباً.',
                'message' => 'تم إرسال طلب الاستقالة بنجاح. سيتم التواصل معك قريباً.'
            ]);
        }
    }
}
