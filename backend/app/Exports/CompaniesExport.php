<?php

namespace App\Exports;

use App\Models\Back\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompaniesExport implements FromCollection, WithHeadings, WithMapping
{
    public function headings(): array
    {
        return [
            'تاريخ الإنشاء',
            'الشركة',
            'السجل التجاري',
            'عدد المتدربين',
        ];
    }

    public function map($company): array
    {
        return [
            $company->created_at->format('Y-m-d'),
            $company->name_ar,
            $company->cr_number,
            $company->trainees()->count(),
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Company::all();
    }
}