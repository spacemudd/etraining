<?php

namespace App\Exports;

use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use App\Models\Back\Trainee;
use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class AttendanceSheetExport implements FromView, WithEvents
{

    public function __construct($course_batch_session_id)
    {
        $this->course_batch_session_id = $course_batch_session_id;
    }


    public function registerEvents(): array
    {
        if (app()->getLocale() === 'ar') {
            return [
                AfterSheet::class    => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(true);
                },
            ];
        } else {
            return [
                AfterSheet::class    => function(AfterSheet $event) {
                    $event->sheet->getDelegate()->setRightToLeft(false);
                },
            ];
        }
    }

    /**
     * @return \Illuminate\Support\View
     */
    public function view(): View
    {
        $users = CourseBatchSession::with('course')
            ->with(['attendances' => function($q) {
                $q->with('trainee');
            }])
            ->with(['course_batch' => function($q) {
                $q->with(['trainee_group' => function($q) {
                    $q->with('trainees');
                }]);
            }])->findOrFail($this->course_batch_session_id);

        $attendances = $users->attendances;

        $usersWhoDidntAttended = [];

        foreach ($users->course_batch->trainee_group->trainees as $trainee) {
            if (!CourseBatchSessionAttendance::where('id', $this->course_batch_session_id)
                ->where('trainee_id', $trainee->id)->exists()) {
                $usersWhoDidntAttended[] = $trainee;
            }
        }

        if (app()->getLocale() === 'ar') {
            $course_name = $users->course->name_ar;
        } else {
            $course_name = $users->course->name_en;
        }

        return view('exports.attendingSheet', [
            'attendances' => $attendances,
            'users_who_didnt_attend' => $usersWhoDidntAttended,
            'course_name' => $course_name,
        ]);

    }
}
