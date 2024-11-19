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
              where('city_id','24ae4690-3c0f-4627-a0c5-240daf4a9f2a')
             ->whereNull('company_id')
             ->get(['name','phone','identity_number'])
             ->map(function($trainee){
                return [
                    'identity_number' => $trainee->identity_number,
                    'phone' => $trainee->clean_phone,
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
