<?php
/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Exports;

use App\Models\Back\CourseBatchSession;
use App\Models\Back\CourseBatchSessionAttendance;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourseBatchSessionAttendanceSummarySheet implements FromView, WithEvents, WithStyles, WithColumnWidths, WithTitle
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

    public function styles(Worksheet $sheet)
    {
        return [
            'A:Z' => [
                'font' => [
                    'name' => 'Arial',
                    'size' => 12,
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 20,
            'D' => 20,
            'E' => 15,
            'F' => 22,
            'G' => 22,

        ];
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function view(): View
    {
        $users = CourseBatchSession::with('course')
            ->with(['attendances' => function($q) {
                $q->with(['trainee' => function($q) {
                    $q->with('company');
                }]);
            }])
            ->with(['course_batch' => function($q) {
                $q->with(['trainee_group' => function($q) {
                    $q->with(['trainees' => function($q) {
                        $q->withTrashed()->with('company');
                    }]);
                }]);
            }])->findOrFail($this->course_batch_session_id);

        $attendances = $users->attendances;

        $usersWhoDidntAttended = [];

        foreach ($users->course_batch->trainee_group->trainees as $trainee) {
            $hasAttended = CourseBatchSessionAttendance::where('course_batch_session_id', $this->course_batch_session_id)
                ->where('trainee_id', $trainee->id)
                ->first();
            if (!$hasAttended) {
                $usersWhoDidntAttended[] = $trainee;
            }
        }

        if (app()->getLocale() === 'ar') {
            $course_name = $users->course->name_ar;
        } else {
            $course_name = $users->course->name_en;
        }

        return view('exports.attendingSheet', [
            'course_batch' => $users,
            'attendances' => $attendances,
            'users_who_didnt_attend' => $usersWhoDidntAttended,
            'course_name' => $course_name,
        ]);

    }

    public function title(): string
    {
        return 'Summary';
    }
}
