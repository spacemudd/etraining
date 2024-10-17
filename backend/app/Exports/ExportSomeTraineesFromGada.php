<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;


class ExportSomeTraineesFromGada implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Trainee::withTrashed()->where('deleted_remark','رجيع')->where('city_id','e5a4a741-302f-44fa-8c44-06df64e68b6d')
             ->whereBetween('created_at', [Carbon::create(2023, 1, 1), now()])
             ->with('company')
             ->get(['name','phone','identity_number','company_id','created_at','deleted_at','deleted_remark'])
             ->map(function($trainee){
                return [
                    'deleted_remark'=>$trainee->deleted_remark,
                    'deleted_at'=>$trainee->deleted_at,
                    'created_at'=>$trainee->created_at,
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
            'سبب الإيقاف',
            'تاريخ الإيقاف',
            'تاريخ الإنشاء',
            'الشركة',
            'رقم الجوال',
            'رقم الهوية',
            'الإسم'
        ];
    }
}
