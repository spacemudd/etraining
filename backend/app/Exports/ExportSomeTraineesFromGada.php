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
        return Trainee::with(['company', 'invoices' => function ($query) {
                $query->orderBy('grand_total', 'desc'); 
            }])
            ->where('zoho_contract_status', 'completed')
            ->get(['name', 'phone', 'identity_number', 'email', 'company_id'])
            ->map(function ($trainee) {
                $highestInvoice = $trainee->invoices->first();
    
                $invoiceValue = $trainee->company?->monthly_subscription_per_trainee 
                    ?? ($highestInvoice ? $highestInvoice->grand_total : 0);
    
                return [
                    'value' => $invoiceValue, 
                    'email' => $trainee->email,
                    'identity_number' => $trainee->identity_number,
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
            'رقم الجوال',
            'الإسم'
        ];
    }
}

