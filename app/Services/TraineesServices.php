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

use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;

class TraineesServices
{
    /**
     * Create a new trainee.
     *
     * @param $traineeRequest
     * @return Trainee
     */
    public function store($traineeRequest)
    {
        \DB::beginTransaction();
        $trainee = Trainee::create($traineeRequest);
        if (isset($traineeRequest['trainee_group_name'])) {
            $group = TraineeGroup::firstOrCreate([
                'name' => ['trainee_group_name'],
            ]);
            $group->trainees()->attach([$trainee->id]);
        }
        \DB::commit();

        return $trainee;
    }
}
