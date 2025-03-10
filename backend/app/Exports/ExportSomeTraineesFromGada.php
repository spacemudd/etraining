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
        return Trainee::with([
                'company', 
                'invoices' => function ($query) {
                    $query->orderBy('grand_total', 'desc')->limit(1); // ✅ جلب أعلى فاتورة فقط
                }
            ])
            ->where('zoho_contract_status', 'completed')
            ->get(['name', 'phone', 'identity_number', 'email', 'company_id'])
            ->map(function ($trainee) {
                $monthlySubscription = $trainee->company?->monthly_subscription_per_trainee;
                $highestInvoice = $trainee->invoices->first()?->grand_total; // ✅ أعلى فاتورة
    
                // ✅ استخدام أعلى فاتورة إذا لم يكن هناك اشتراك شهري
                $value = $monthlySubscription ?? $highestInvoice ?? 0;
    
                return [
                    'subscription_or_invoice' => $value, // ✅ الآن يتم تحديد القيمة بشكل صحيح
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

