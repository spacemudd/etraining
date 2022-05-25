<?php

namespace App\Services;

use App\Models\Back\TraineeCompanyMovement;

class TraineeCompanyMovementService
{
    public function recordMovement($trainee_id, $new_company_id, $old_company_id=null)
    {
        if ($new_company_id) {
            TraineeCompanyMovement::create([
                'trainee_id' => $trainee_id,
                'company_id' => $new_company_id,
                'in_date' => now(),
            ]);
        }

        if ($old_company_id) {
            TraineeCompanyMovement::create([
                'trainee_id' => $trainee_id,
                'company_id' => $old_company_id,
                'out_date' => now(),
            ]);
        }
    }
}
