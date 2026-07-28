<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RecentUnlinkedTraineesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'الاسم',
            'رقم الهوية',
            'رقم الجوال',
            'البريد الإلكتروني',
            'تاريخ الإنشاء',
        ];
    }

    public function map($trainee): array
    {
        return [
            $trainee->name,
            $trainee->identity_number,
            $trainee->phone,
            $trainee->email,
            optional($trainee->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function collection()
    {
        return Trainee::query()
            ->whereNull('company_id')
            ->where('created_at', '>=', Carbon::now()->subMonths(3))
            ->orderByDesc('created_at')
            ->get([
                'id',
                'name',
                'identity_number',
                'phone',
                'email',
                'created_at',
            ]);
    }
}
