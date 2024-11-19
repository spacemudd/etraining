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
              where('city_id','e5a4a741-302f-44fa-8c44-06df64e68b6d')
             ->whereNull('company_id')
             ->get(['name','phone','identity_number'])
             ->map(function($trainee){
                return [
                    'identity_number' => $trainee->identity_number,
                    'phone'=> $trainee->phone,
                    'name' => $trainee->name,
                ];
             });
    }

    public function headings(): array
    {
        return [
            'رقم الهوية',
            'رقم الجوال',
            'الإسم'
        ];
    }
}
