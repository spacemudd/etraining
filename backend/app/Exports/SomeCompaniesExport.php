<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SomeCompaniesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Trainee::whereNull('deleted_at')
            ->whereNull('suspended_at')
            ->orderBy('created_at', 'desc')
            ->get(['name', 'phone as phone_number']);
    }

    public function headings(): array
    {
        return [
            'اسم المتدرب',
            'رقم الجوال',
        ];
    }
}



