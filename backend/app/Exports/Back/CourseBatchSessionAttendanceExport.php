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

namespace App\Exports\Back;

use App\Models\Back\CourseBatchSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CourseBatchSessionAttendanceExport implements FromCollection, WithMapping, WithHeadings
{
    public $session;

    public function __construct(CourseBatchSession $session)
    {
        $this->session = $session;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->session->attendances()->get();
    }

    public function map($attendance): array
    {
        return [
            'trainee' => $attendance->trainee->name,
            'phone' => $attendance->trainee->phone,
            'attended_at' => $attendance->attended_at,
        ];
    }

    public function headings(): array
    {
        return [
            'Trainee',
            'Phone',
            'Attended',
        ];
    }
}
