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
        // جلب جميع الفواتير المدفوعة في سنة 2024
        $invoices = Invoice::where('status', Invoice::STATUS_PAID)
            ->whereYear('from_date', 2024)
            ->with(['company.region'])
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

            // تهيئة مصفوفة الشهور (1-12)
            $monthlyCounts = array_fill(1, 12, 0);

            // تجميع الفواتير حسب الشهر
            foreach ($companyInvoices as $invoice) {
                $month = (int) $invoice->from_date->format('n'); // n = month without leading zeros (1-12)
                if ($month >= 1 && $month <= 12) {
                    $monthlyCounts[$month]++;
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

        // إضافة أسماء الشهور العربية
        $months = [
            'يناير',
            'فبراير',
            'مارس',
            'إبريل',
            'مايو',
            'يونيو',
            'يوليو',
            'أغسطس',
            'سبتمبر',
            'أكتوبر',
            'نوفمبر',
            'ديسمبر',
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

        // إضافة عدد الفواتير لكل شهر (1-12)
        for ($month = 1; $month <= 12; $month++) {
            $row[] = $monthlyCounts[$month] ?? 0;
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

