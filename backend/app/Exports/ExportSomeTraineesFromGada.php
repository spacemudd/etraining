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
        // ✅ جلب المتدربين الذين عقودهم مكتملة فقط
        $trainees = Trainee::where('zoho_contract_status', 'completed')
            ->whereNotNull('zoho_contract_id')
            ->get(); // نحصل فقط على المتدربين الذين نحتاجهم
    
        return $trainees->map(function ($trainee) {
            // ✅ الحصول على قيمة الاشتراك الشهري إن وجدت
            $monthlySubscription = $trainee->company?->monthly_subscription_per_trainee;
    
            // ✅ إذا لم يكن هناك اشتراك شهري، نحصل على أعلى فاتورة
            if (is_null($monthlySubscription)) {
                $highestInvoice = $trainee->invoices()->orderByDesc('grand_total')->first();
                $monthlySubscription = $highestInvoice?->grand_total ?? 0;
            }
    
            return [
                'subscription_or_invoice' => $monthlySubscription,
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

