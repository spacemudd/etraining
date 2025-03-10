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

        // $idNumbers = [

        // ];
        return Trainee::with(['company', 'invoices'])->where('zoho_contract_status','completed')->get(['name', 'phone', 'identity_number', 'email', 'company_id'])->map(function ($trainee) {
            $monthlySubscription = $trainee->company ? $trainee->company->monthly_subscription_per_trainee : null;
        
            if (is_null($monthlySubscription)) {
                $highestGrandTotal = $trainee->invoices()->max('grand_total') ?? 0; 
                return [
                    'value' => $highestGrandTotal,
                    'email' => $trainee->email,
                    'identity_number' => $trainee->identity_number,
                    'phone' => $trainee->clean_phone,
                    'name' => $trainee->name,
                ];
            }
        
            return [
                'value' => $monthlySubscription,
                'email' => $trainee->email,
                'identity_number' => $trainee->identity_number,
                'phone' => $trainee->clean_phone,
                'name' => $trainee->name,
            ];
        });



    
        // return Trainee::whereDoesntHave('company')
        //     ->get(['name', 'phone', 'identity_number', 'email', 'city_id']) 
        //     ->map(function ($trainee) {
        //         return [
        //             'city' => $trainee->city?->name_ar ?? 'لا يوجد',
        //             'email' => $trainee->email,
        //             'identity_number' => $trainee->identity_number,
        //             'phone' => $trainee->clean_phone,
        //             'name' => $trainee->name,
        //         ];
        //     });
    }

    public function headings(): array
    {
        return [
            'قيمة الاشتراك',
            'الإيميل',
            'رقم الهوية',
            'رقم الجوال',
            'الإسم'
        ];
    }
}

