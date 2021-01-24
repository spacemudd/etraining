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
use Maatwebsite\Excel\Concerns\FromCollection;

class CourseBatchSessionAttendanceExport implements FromCollection
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
        return $this->attendances()->get();
    }
}
