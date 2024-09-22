<?php

namespace App\Services;

use App\Models\Back\Trainee;
use App\Models\Back\TraineeCompanyMovement;

class TraineeCompanyMovementService
{
    public function recordMovement($trainee_id, $new_company_id, $old_company_id=null)
    {
        $trainee = Trainee::withTrashed()->find($trainee_id);
        if ($new_company_id) {
            TraineeCompanyMovement::create([
                'trainee_id' => $trainee_id,
                'trainee_name' => $trainee->name,
                'trainee_identity_number' => $trainee->identity_number,
                'trainee_phone_number' => $trainee->phone,
                'company_id' => $new_company_id,
                'in_date' => now(),
            ]);
        }

        if ($old_company_id) {
            TraineeCompanyMovement::create([
                'trainee_id' => $trainee_id,
                'trainee_name' => $trainee->name,
                'trainee_identity_number' => $trainee->identity_number,
                'trainee_phone_number' => $trainee->phone,
                'company_id' => $old_company_id,
                'out_date' => now(),
            ]);
        }
    }
}
