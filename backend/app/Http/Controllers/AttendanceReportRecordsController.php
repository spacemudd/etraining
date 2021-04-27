<?php

namespace App\Http\Controllers;

use App\Models\Back\AttendanceReportRecord;
use Illuminate\Http\Request;

class AttendanceReportRecordsController extends Controller
{
    public function update(Request $request, $id)
    {
        $request->validate([
            'absent_reason' => 'string|max:255',
            'status' => 'string|max:255',
        ]);

        $record = AttendanceReportRecord::with('trainee')->findOrFail($id);

        if ($request->status === 'absent') {
            $record->status = AttendanceReportRecord::STATUS_ABSENT;
            $record->absence_reason = null;
        }

        if ($request->status === 'absent-with-excuse') {
            $record->status = AttendanceReportRecord::STATUS_ABSENT_WITH_EXCUSE;
            $record->absence_reason = $request->absence_reason;
        }

        if ($request->status === 'present-but-late') {
            $record->status = AttendanceReportRecord::STATUS_LATE_TO_CLASS;
            $record->absence_reason = null;
        }

        if ($request->status === 'present') {
            $record->status = AttendanceReportRecord::STATUS_PRESENT;
            $record->absence_reason = null;
        }

        $record->save();

        return $record;
    }
}
