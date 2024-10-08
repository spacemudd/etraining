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
        ->select('name', 'identity_number', 'phone','created_at','deleted_at')
        ->where('city_id', 'd4fb0162-81ec-4b17-812a-06c7c4306cb5')
        ->whereDate('created_at', '<=', '2023-06-30')
        ->where(function ($query) {
            $query->whereNotNull('deleted_at') ;
                //   ->orWhere('company_id', null); 
        })
        ->get()
        ->map(function ($trainee) {
            return [
                'deleted_at' =>$trainee->deleted_at,
                'created_at' =>$trainee->created_at,
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
            'تاريخ الحذف',
            'تاريخ التسجيل',
            'الحالة',
            'رقم الجوال',
            'رقم الهوية',
            'اسم المتدرب',
        ];
    }
}
