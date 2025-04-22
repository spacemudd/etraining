<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ExportSomeTraineesFromGada implements FromCollection, WithHeadings
{
    public function collection()
    {
        ini_set('max_execution_time', 300); // زيادة وقت التنفيذ

        $date = Carbon::parse('2025-01-01');

        $trainees = Trainee::with('user')
            ->whereHas('user', function ($q) use ($date) {
                $q->where('last_login_at', '<', $date);
            })
            ->get();

        return $trainees->map(function ($trainee) {
            return [
                'name' => $trainee->name,
                'identity_number' => $trainee->identity_number,
                'email' => $trainee->email,
                'phone' => $trainee->phone,
                'company' => $trainee->company->name_ar ?? '-', // لو ما في شركة يظهر "-"
                'last_login_at' => optional($trainee->user)->last_login_at ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'الإسم',
            'الهوية',
            'الإيميل',
            'الجوال',
            'الشركة',
            'تاريخ آخر دخول',
        ];
    }
}
