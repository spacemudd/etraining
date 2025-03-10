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
        return Trainee::with(['company'])
            ->where('zoho_contract_status', 'completed')
            ->withSum('invoices as highest_grand_total', 'grand_total')
            ->get(['name', 'phone', 'identity_number', 'email', 'company_id'])
            ->map(function ($trainee) {
                $monthlySubscription = $trainee->company ? $trainee->company->monthly_subscription_per_trainee : null;
    
                $invoiceValue = $monthlySubscription ?? $trainee->highest_grand_total ?? 0;
    
                return [
                    'value' => $invoiceValue, 
                    'email' => $trainee->email,
                    'identity_number' => $trainee->identity_number,
                    'phone' => $trainee->clean_phone,
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
            'رقم الجوال',
            'الإسم'
        ];
    }
}

