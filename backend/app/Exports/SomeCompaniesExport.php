<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SomeCompaniesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Trainee::withTrashed()
        ->where('city_id','d4fb0162-81ec-4b17-812a-06c7c4306cb5')
        ->whereBetween('created_at', ['2021-01-01 00:00:00', '2023-12-31 23:59:59'])
        ->where(function($q) {
            $q->whereNotNull('suspended_at')
              ->orWhereNotNull('deleted_at');
        })
        ->get()
        ->map(function($trainee) {
            return [
                'name' => $trainee->name,
                'identity_number' => $trainee->identity_number,
                'phone_number' => $trainee->phone,
                'deleted_at' => $trainee->deleted_at,
                'suspended_at' => $trainee->suspended_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'اسم المتدرب',
            'رقم الهوية',
            'رقم الجوال',
            'تاريخ الحذف',
            'تاريخ الإيقاف',
        ];
    }
}


