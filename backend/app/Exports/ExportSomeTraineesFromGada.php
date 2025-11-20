<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use App\Models\Back\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ExportSomeTraineesFromGada implements FromCollection, WithHeadings
{
    public function collection()
    {
        // $invoiceIds=[
        //     'fde280f6-2329-4aba-98f5-314a30f9245b',  
        //     '007efc69-46ca-44c8-ab03-c12dad6eb802',  
           

        // ];

        // $trainees = Trainee::withTrashed()->whereIn('id', function ($query) use ($invoiceIds) {
        //     $query->select('trainee_id')
        //         ->from('invoices')
        //         ->whereIn('id', $invoiceIds);
        // })->get();


        $trainees = Trainee::onlyTrashed()
            ->where('educational_level_id', '3a995aa0-94aa-4081-97d2-2e4c559cea22')
            ->where('city_id', 'e5a4a741-302f-44fa-8c44-06df64e68b6d')
            ->where('status', '!=', '2')
            ->with('company') // إضافة eager loading لتجنب N+1 problem
            ->get();

        return $trainees->map(function ($trainee) {
            return [
                'identity_number' => $trainee->identity_number, // الهوية
                'company' => $trainee->company?->name_ar ?? 'غير محدد', // الشركة - استخدام null-safe operator
                'phone' => $trainee->phone, // الجوال
                'name' => $trainee->name, // الإسم
            ];
        });
    }

    public function headings(): array
    {
        return [
            'الهوية',
            'الشركة',
            'الجوال',
            'الإسم',
        ];
    }
}
