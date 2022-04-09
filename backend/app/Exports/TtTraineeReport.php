<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class TtTraineeReport implements FromArray
{
    /**
    * @return array
     */
    public function array(): array
    {
        $trainees = Trainee::where('company_id', '!=', null)
            ->where('suspended_at', null)
            ->where('deleted_remark', null)
            ->where('trainee_group_id', '!=', null)
            ->where('deleted_at', null)
            ->with('company')
            ->whereHas('company', function($q) {$q->where('deleted_at', null);})
            ->with([
                'absences_03to07',
            ])
            ->withCount([
                'absences_03to07',
            ])
            ->get();

        $traineeData = [];

        $traineeData[] = [
            'name',
            'company',
            'email',
            'phone',
            'instructor',
            'group',
            'absences_03_to_07',
        ];

        foreach ($trainees as $trainee) {
            $traineeData[] = [
                'name' => $trainee->name,
                'company' => optional($trainee->company)->name_ar,
                'email' => $trainee->email,
                'phone' => $trainee->phone,
                'instructor' => optional($trainee->instructor)->name,
                'group' => optional($trainee->trainee_group)->name,
                'absences_03to07' => $trainee->absences_03to07_count ?: 0,
            ];
        }

        return $traineeData;
    }
}
