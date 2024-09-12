<?php

namespace App\Exports;

use App\Models\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class TraineesWithCompaniesExport implements FromCollection , WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    
    */
    protected $trainees;

    public function __construct($trainees)
    {
        $this->trainees = $trainees;
    }

    public function collection()
    {
        return collect($this->trainees);

    }
    public function headings(): array
    {
        return [
            'اسم المتدرب',
            'اسم الشركة',
            'رقم الهوية',
        ];
    }
}
