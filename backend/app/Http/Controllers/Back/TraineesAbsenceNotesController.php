<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\AttendanceReportRecord;
use App\Models\Back\AttendanceReportRecordAbsenceNote;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class TraineesAbsenceNotesController extends Controller
{
    public function index()
    {
        $absence_notes = QueryBuilder::for(AttendanceReportRecordAbsenceNote::class)
            ->with(['trainee' => function($q) {
                $q->with('company');
            }])->with(['attendance_report_record' => function($q) {
                $q->with('course_batch_session.course_batch.course');
            }])->with('files')
            ->allowedFilters(['trainee.name', 'trainee.identity_number'])
            ->defaultSort('-created_at')
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Trainees/TraineesAbsenceNotes/Index', [
            'absence_notes' => $absence_notes,
        ])->table(function($table) {
            $table->disableGlobalSearch();
            $table->addSearchRows([
                'trainee.name' => __('words.name'),
                'trainee.identity_number' => __('words.identity_number'),
            ]);
        });
    }

    public function approve($id)
    {
        $note = AttendanceReportRecordAbsenceNote::find($id);
        $note->approved_at = now();
        $note->save();

        $note->attendance_report_record->update([
            'status' => AttendanceReportRecord::STATUS_ABSENT_WITH_EXCUSE,
        ]);

        return redirect()->route('back.trainees.absence-notes.index');
    }

    public function reject($id)
    {
        $note = AttendanceReportRecordAbsenceNote::find($id);
        $note->rejected_at = now();
        $note->save();

        return redirect()->route('back.trainees.absence-notes.index');
    }
}
