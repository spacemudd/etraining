<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class ExportSomeTraineesFromGada implements FromCollection, WithHeadings
{
    public function collection()
    {
        $trainees = Trainee::where('zoho_contract_status', 'completed')
            ->whereNotNull('zoho_contract_id')
            ->get(); 
    
        return $trainees->map(function ($trainee) {
            $monthlySubscription = $trainee->override_training_costs ?? 2300;
    
         
            return [
                'subscription_or_invoice' => $monthlySubscription,
                'email' => $trainee->email,
                'identity_number' => $trainee->identity_number,
                'company_name' =>$trainee->company->name_ar,
                'phone' => $trainee->phone,
                'name' => $trainee->name,
            ];
        });
    }

    


    public function headings(): array
    {
        return [
            'قيمة الاشتراك',
            'الإيميل',
            'رقم الهوية',
            'الشركة',
            'رقم الجوال',
            'الإسم'
        ];
    }
}

