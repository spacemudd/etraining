<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportSomeTraineesFromGada implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Trainee::onlyTrashed()->where('deleted_remark','غير مسجلة في الشركة')->where('city_id','e5a4a741-302f-44fa-8c44-06df64e68b6d')
             ->where('created_at', '>=', '2023-01-01')
             ->with('company')
             ->get(['name','phone','identity_number','company_id'])
             ->map(function($trainee){
                return [
                    'company'=> optional($trainee->company)->name_ar,
                    'phone'=> $trainee->phone,
                    'identity_number'=> $trainee->identity_number,
                    'name' => $trainee->name,
                ];
             });
    }

    public function headings(): array
    {
        return [
            'الشركة',
            'رقم الجوال',
            'رقم الهوية',
            'الإسم'
        ];
    }
}
