<?php

namespace App\Services;

use App\Models\Back\TraineeCompanyMovement;

class TraineeCompanyMovementService
{
    public function recordMovement($trainee_id, $new_company_id, $old_company_id=null)
    {
        if (!$old_company_id) { // first time being assigned to a company.
            TraineeCompanyMovement::create([
                'trainee_id' => $trainee_id,
                'company_id' => $new_company_id,
                'in_date' => now(),
            ]);
        }

        $q = TraineeCompanyMovement::query()->where('trainee_id', $trainee_id);

        $pendingOutMovement = $q->where('old_company_id', $old_company_id)
            ->where('in_date', '!=', null)
            ->where('out_date', null)
            ->first();

        if ($pendingOutMovement) {
            $pendingOutMovement->out_date = now();
            $pendingOutMovement->save();
        }

        if (!$pendingOutMovement) {
            TraineeCompanyMovement::create([
                'trainee_id' => $trainee_id,
                'company_id' => $new_company_id
            ]);
        }
    }
}
