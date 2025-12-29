<?php

namespace App\Exports;

use App\Models\Back\Invoice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CompaniesPaidInvoices2024Export implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $companiesData;

    public function __construct()
    {
        // استخدام استعلام أكثر كفاءة مع select محدد
        // الفترة من ديسمبر 2024 إلى ديسمبر 2025
        $invoices = Invoice::where('status', Invoice::STATUS_PAID)
            ->whereBetween('from_date', ['2024-12-01', '2025-12-31'])
            ->whereNotNull('company_id')
            ->select('id', 'company_id', 'from_date')
            ->with(['company:id,name_ar,salesperson_name,region_id', 'company.region:id,name'])
            ->get();

        // تجميع البيانات حسب الشركة والشهر
        $companiesData = collect();

        // تجميع الفواتير حسب company_id
        $invoicesByCompany = $invoices->groupBy('company_id');

        foreach ($invoicesByCompany as $companyId => $companyInvoices) {
            // تخطي الفواتير بدون شركة
            if (!$companyId) {
                continue;
            }

            $company = $companyInvoices->first()->company;
            
            if (!$company) {
                continue;
            }

            // تهيئة مصفوفة الشهور (13 شهر: ديسمبر 2024 + 12 شهر 2025)
            // المفتاح: '2024-12', '2025-01', '2025-02', ..., '2025-12'
            $monthlyCounts = [];

            // تجميع الفواتير حسب الشهر والسنة
            foreach ($companyInvoices as $invoice) {
                if ($invoice->from_date) {
                    $year = (int) $invoice->from_date->format('Y');
                    $month = (int) $invoice->from_date->format('n');
                    
                    // فقط الشهور من ديسمبر 2024 إلى ديسمبر 2025
                    if (($year == 2024 && $month == 12) || ($year == 2025 && $month >= 1 && $month <= 12)) {
                        $key = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
                        if (!isset($monthlyCounts[$key])) {
                            $monthlyCounts[$key] = 0;
                        }
                        $monthlyCounts[$key]++;
                    }
                }
            }

            $companiesData->push([
                'company' => $company,
                'monthly_counts' => $monthlyCounts,
            ]);
        }

        // ترتيب حسب اسم الشركة
        $this->companiesData = $companiesData->sortBy(function ($item) {
            return $item['company']->name_ar ?? '';
        })->values();
    }

    public function headings(): array
    {
        $headings = [
            'اسم الشركة',
            'اسم الأخصائي',
            'المنطقة',
        ];

        // إضافة أسماء الشهور العربية مع السنة (ديسمبر 2024 إلى ديسمبر 2025)
        $months = [
            'ديسمبر 2024',
            'يناير 2025',
            'فبراير 2025',
            'مارس 2025',
            'إبريل 2025',
            'مايو 2025',
            'يونيو 2025',
            'يوليو 2025',
            'أغسطس 2025',
            'سبتمبر 2025',
            'أكتوبر 2025',
            'نوفمبر 2025',
            'ديسمبر 2025',
        ];

        return array_merge($headings, $months);
    }

    public function map($item): array
    {
        $company = $item['company'];
        $monthlyCounts = $item['monthly_counts'];

        $row = [
            $company->name_ar ?? '',
            $company->salesperson_name ?? '',
            optional($company->region)->name ?? '',
        ];

        // إضافة عدد الفواتير لكل شهر (ديسمبر 2024 إلى ديسمبر 2025)
        // الترتيب: 2024-12, 2025-01, 2025-02, ..., 2025-12
        $monthKeys = [
            '2024-12',
            '2025-01',
            '2025-02',
            '2025-03',
            '2025-04',
            '2025-05',
            '2025-06',
            '2025-07',
            '2025-08',
            '2025-09',
            '2025-10',
            '2025-11',
            '2025-12',
        ];

        foreach ($monthKeys as $key) {
            $row[] = $monthlyCounts[$key] ?? 0;
        }

        return $row;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->companiesData;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setRightToLeft(true);
            },
        ];
    }
}

