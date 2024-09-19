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
            'المركز',
            'جهة التوظيف',
            'الشركة',
            'المنطقة',
            'الايميل',
            'ايميل المندوب',
            'السجل التجاري',
            'عدد المتدربين',
        ];
    }

    public function map($company): array
    {
        return [
            $company->code,
            $company->created_at->format('Y-m-d'),
            optional($company->center)->name,
            optional($company->recruitmentCompany)->name,
            $company->name_ar,
            optional($company->region)->name,
            $company->email,
            $company->salesperson_email,
            $company->cr_number,
            $company->trainees_count,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Company::with('center', 'region', 'recruitmentCompany')->withCount('trainees')->get();
    }
}
