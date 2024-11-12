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
              whereNull('deleted_at')
             ->whereNull('suspended_at')
             ->get(['name','phone'])
             ->map(function($trainee){
                return [
             
                    'phone'=> $trainee->phone,
                    'name' => $trainee->name,
                ];
             });
    }

    public function headings(): array
    {
        return [
            'رقم الجوال',
            'الإسم'
        ];
    }
}
