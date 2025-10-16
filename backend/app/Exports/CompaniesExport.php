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
            'الشركة',
            'طبيعة العمل',
            'المنطقة',
            'الايميل',
            'اسم المندوب',
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
            $company->name_ar,
            $company->nature_of_work,
            optional($company->region)->name,
            $company->email,
            $company->salesperson_name,
            $company->cr_number,
            $company->trainees_count,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Company::with('center', 'region')->withCount('trainees')->get();
    }
}
