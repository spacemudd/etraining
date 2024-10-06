<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TraineesExportBlocked implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Trainee::withTrashed()
        ->select('name', 'identity_number', 'phone', 'deleted_at')
        ->where('city_id', 'e5a4a741-302f-44fa-8c44-06df64e68b6d')
        ->whereBetween('created_at', ['2023-06-01', now()])
        ->where(function ($query) {
            $query->whereNotNull('deleted_at') 
                  ->orWhere('company_id', null); 
        })
        ->get()
        ->map(function ($trainee) {
            return [
                'status' => $trainee->deleted_at ? 'موقوف' : 'بدون شركة',
                'phone' => $trainee->phone,
                'identity_number' => $trainee->identity_number,
                'name' => $trainee->name,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'الحالة',
            'رقم الجوال',
            'رقم الهوية',
            'اسم المتدرب',
        ];
    }
}
