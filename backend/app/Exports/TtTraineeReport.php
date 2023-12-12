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
            ->with(['company'])
            ->whereHas('company', function($q) {$q->where('deleted_at', null);})
            ->with([
                'absences_custom',
            ])
            ->withCount([
                'absences_custom',
            ])
            ->limit(20)
            ->get();

        $traineeData = [];

        $traineeData[] = [
            'name',
            'company',
            'email',
            'phone',
            'instructor',
            'group',
            'absences_custom',
        ];

        foreach ($trainees as $trainee) {
            $traineeData[] = [
                'name' => $trainee->name,
                'company' => optional($trainee->company)->name_ar,
                'email' => $trainee->email,
                'phone' => $trainee->clean_phone,
                'absences_custom' => $trainee->absences_custom_count ?: 0,
                'oldest_absence' => $trainee->absences_custom()->oldest()->first(),
                'latest_absence' => $trainee->absences_custom()->latest()->first(),
            ];
        }

        return $traineeData;
    }
}
