<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SomeCompaniesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Trainee::where('trainee_group_id', '72875db8-a734-4631-a579-a7ebb1312b87')
            ->with('company')
            ->get()
            ->map(function ($trainee) {
                return [
                    'company_name' => optional($trainee->company)->name_ar, 
                ];
            })
            ->unique('company_name');
    }

    public function headings(): array
    {
        return ['Company Name'];
    }
}


