<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SomeCompaniesExport implements FromCollection, WithHeadings
{
    protected $traineeGroupId;

    // public function __construct($traineeGroupId)
    // {
    //     $this->traineeGroupId = $traineeGroupId;
    // }

    public function collection()
    {
        return Trainee::where('trainee_group_id','72875db8-a734-4631-a579-a7ebb1312b87')
            ->with('company') 
            ->get()
            ->pluck('company.name_ar') 
            ->unique(); 
    }

    public function headings(): array
    {
        return ['Company Name']; 
    }
}

