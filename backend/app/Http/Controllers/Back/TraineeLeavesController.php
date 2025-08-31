<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\TraineeLeave;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TraineeLeavesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);
        $leaves = TraineeLeave::where('trainee_id', $trainee_id)
            ->with('trainee')
            ->orderBy('created_at', 'desc')
            ->get();

        // تحديث روابط الملفات لتعمل مع المنفذ الصحيح (هذا الآن يتم في النموذج)
        // $leaves->each(function ($leave) {
        //     // الرابط الآن يتم إصلاحه في النموذج TraineeLeave
        // });

        return response()->json($leaves);
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
        $leave = TraineeLeave::where('trainee_id', $trainee_id)
            ->where('id', $id)
            ->firstOrFail();

        $leave->delete();

        return response()->json(['message' => 'تم حذف طلب الإجازة بنجاح']);
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
}

