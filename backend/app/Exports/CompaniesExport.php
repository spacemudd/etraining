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
            'الرقم',
            'تاريخ الإنشاء',
            'الشركة',
            'الايميل',
            'السجل التجاري',
            'عدد المتدربين',
        ];
    }

    public function map($company): array
    {
        return [
            $company->id,
            $company->created_at->format('Y-m-d'),
            $company->name_ar,
            $company->email,
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
