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
            ->where('city_id', '24ae4690-3c0f-4627-a0c5-240daf4a9f2a')
            ->whereBetween('created_at', ['2021-01-01 00:00:00', '2023-12-31 23:59:59'])
            ->where(function($q) {
                $q->whereNotNull('suspended_at')
                  ->orWhereNotNull('deleted_at');
            })
            ->orderBy('created_at', 'desc')  
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



