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
    return Trainee::where('trainees.zoho_contract_status', 'completed') // ✅ تصفية المتدربين قبل الـ JOIN
        ->whereNotNull('trainees.zoho_contract_id') // ✅ التأكد من وجود عقد
        ->leftJoin('companies', 'trainees.company_id', '=', 'companies.id')
        ->leftJoin('invoices', function ($join) {
            $join->on('trainees.id', '=', 'invoices.trainee_id')
                 ->whereRaw('invoices.grand_total = (SELECT MAX(grand_total) FROM invoices WHERE invoices.trainee_id = trainees.id)');
        })
        ->select([
            'trainees.name',
            'trainees.phone',
            'trainees.identity_number',
            'trainees.email',
            DB::raw('COALESCE(companies.monthly_subscription_per_trainee, invoices.grand_total, 0) as subscription_or_invoice')
        ])
        ->get()
        ->map(function ($trainee) {
            return [
                'subscription_or_invoice' => $trainee->subscription_or_invoice,
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

