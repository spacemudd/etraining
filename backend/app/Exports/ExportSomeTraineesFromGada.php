<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ExportSomeTraineesFromGada implements FromCollection, WithHeadings
{
    public function collection()
    {
        $idNumbers=[
  
        ];

        $trainees = Trainee::where('override_training_costs',0)
        ->get();
    

        return $trainees->map(function ($trainee) {
            return [
                'name' => $trainee->name,
                'identity_number' => $trainee->identity_number,
                'email' => $trainee->email,
                'phone' => $trainee->phone,
                'company' => $trainee->company->name_ar ?? '-',
                'last_login_at' => optional($trainee->user)->last_login_at ?? '-',
                'created_at'=> $trainee->created_at,
                'status' =>$trainee->status,
                'zoho_contract_status' => $trainee->zoho_contract_status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'الإسم',
            'الهوية',
            'الإيميل',
            'الجوال',
            'الشركة',
            'تاريخ آخر دخول',
            'تاريخ التسجيل',
            'الحالة',
            'حالة العقد',
        ];
    }
}
