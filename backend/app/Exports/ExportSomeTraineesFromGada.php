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
        return Trainee::
            whereDoesntHave('company')
            //   where('city_id','8e089244-0763-47d4-9ddb-122bab61e0ee')
            //  ->whereNull('company_id')
            //   whereNotNull('trainee_group_id')
            //   where('company_id','3b5b63f7-223a-404b-bb0e-90d7a1af1ff5')
             ->get(['name','phone','identity_number','email'])
             ->map(function($trainee){
                return [
                    'city' =>$trainee->city ? $trainee->city->name_ar : 'لا يوجد',
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
