<?php

namespace App\Http\Controllers;

use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReportRecordAbsenceNote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceReportRecordAbsenceNotesController extends Controller
{
    // create an index page for the attendance report record absence notes
    public function index()
    {
        return Inertia::render('Back/Trainees/AttendanceReportRecordAbsenceNotes/Index', [
            'records' => AttendanceReportRecordAbsenceNote::with(['trainee', 'attendance_report_record'])
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }

    // create a create method page
    public function create($attendance_report_record_id)
    {
        $attendance_report_record = AttendanceReportRecord::with('course_batch_session.course')
            ->find($attendance_report_record_id);

        return Inertia::render('Trainees/AttendanceReportRecordAbsenceNotes/Create', [
            'attendance_report_record' => $attendance_report_record,
        ]);
    }

    // store the note and save the file
    public function store(Request $request, $attendance_report_record_id)
    {
        $request->validate([
            'files' => 'nullable',
        ]);

        $attendance_report_record_absence_note = new AttendanceReportRecordAbsenceNote();
        $attendance_report_record_absence_note->trainee_id = auth()->user()->trainee->id;
        $attendance_report_record_absence_note->attendance_report_record_id = $attendance_report_record_id;
        $attendance_report_record_absence_note->save();

        if ($request->hasFile('files')) {
            $attendance_report_record_absence_note->addMedia($request->file('files')[0])->toMediaCollection('files');
        }

        return redirect()->route('trainees.attendance-sheet.index');
    }
}
