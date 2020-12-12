<?php
/**
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Services;

use App\Models\Back\Instructor;
use App\Models\Team;

class InstructorServices
{
    /**
     * Create a new instructor.
     *
     * @param $instructorRequest
     * @return Instructor
     */
    public function store($instructorRequest)
    {
        \DB::beginTransaction();
        $instructor = Instructor::make($instructorRequest);
        $instructor->team_id = Team::first()->id; // TODO: Make it tenant-ready.
        $instructor->save();
        \DB::commit();

        return $instructor;
    }
}
