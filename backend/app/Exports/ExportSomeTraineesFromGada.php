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
        return Trainee::whereDoesntHave('company')
            ->get(['name', 'phone', 'identity_number', 'email', 'city_id']) 
            ->map(function ($trainee) {
                return [
                    'city' => $trainee->city?->name_ar ?? 'لا يوجد',
                    'email' => $trainee->email,
                    'identity_number' => $trainee->identity_number,
                    'phone' => $trainee->clean_phone,
                    'name' => $trainee->name,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'المنطقة',
            'الإيميل',
            'رقم الهوية',
            'رقم الجوال',
            'الإسم'
        ];
    }
}

