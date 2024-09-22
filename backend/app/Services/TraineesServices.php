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
use App\Models\Team;

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
        $trainee = Trainee::make($traineeRequest);
        $trainee->team_id = Team::first()->id; // TODO: Make it tenant-ready.
        $trainee->company_id = $traineeRequest['company_id'] ?? null;
        if (isset($traineeRequest['trainee_group_name'])) {
            $group = TraineeGroup::firstOrCreate([
                'name' => $traineeRequest['trainee_group_name'],
            ]);
            $trainee->trainee_group_id = $group->id;
        }
        $trainee->save();
        \DB::commit();

        return $trainee;
    }

    public function verifyPhoneOwnership()
    {

    }
}
