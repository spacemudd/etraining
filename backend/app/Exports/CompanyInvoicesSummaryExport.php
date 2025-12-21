<?php

namespace App\Exports;

use App\Models\Back\Company;
use App\Models\Back\Invoice;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanyInvoicesSummaryExport implements FromArray, WithHeadings, WithCustomCsvSettings
{
    public $data;
    public $months = [];

    function __construct($data)
    {
        $this->data = $data;
        $this->generateMonthsList();
    }

    /**
     * Generate list of months between date_from and date_to
     */
    private function generateMonthsList(): void
    {
        $startDate = Carbon::parse($this->data['date_from']);
        $endDate = Carbon::parse($this->data['date_to']);

        $current = $startDate->copy()->startOfMonth();
        $end = $endDate->copy()->startOfMonth();

        while ($current->lte($end)) {
            $this->months[] = [
                'year' => $current->year,
                'month' => $current->month,
                'start' => $current->copy()->startOfMonth(),
                'end' => $current->copy()->endOfMonth(),
                'name' => $current->format('F Y'), // e.g., "September 2025"
                'name_ar' => $this->getArabicMonthName($current->month) . ' ' . $current->year,
            ];
            $current->addMonth();
        }
    }

    /**
     * Get Arabic month name
     */
    private function getArabicMonthName(int $month): string
    {
        $months = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر',
        ];

        return $months[$month] ?? '';
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => PHP_EOL,
            'use_bom' => true,
            'output_encoding' => 'UTF-8',
        ];
    }

    public function headings(): array
    {
        $headings = ['اسم الشركة', 'طبيعة العمل', 'المنطقة'];

        foreach ($this->months as $month) {
            $headings[] = $month['name_ar'];
            $headings[] = 'تم الفوترة؟';
            $headings[] = 'عدد الفواتير';
        }

        return $headings;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $startDate = Carbon::parse($this->data['date_from'])->startOfDay();
        $endDate = Carbon::parse($this->data['date_to'])->endOfDay();

        // Get all companies (excluding soft-deleted) with region relationship
        $companies = Company::whereNull('deleted_at')
            ->with('region')
            ->orderBy('name_ar')
            ->get();

        $results = [];

        foreach ($companies as $company) {
            $row = [
                $company->name_ar,
                $company->nature_of_work ?? '',
                $company->region ? $company->region->name : '',
            ];

            // Get all invoices for this company within the date range
            $invoices = Invoice::where('company_id', $company->id)
                ->whereNull('deleted_at')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('from_date', [$startDate, $endDate])
                        ->orWhereBetween('to_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('from_date', '<=', $startDate)
                                ->where('to_date', '>=', $endDate);
                        });
                })
                ->get();

            // For each month, check if company was invoiced
            foreach ($this->months as $month) {
                $monthStart = $month['start'];
                $monthEnd = $month['end'];

                // Count invoices that overlap with this month
                $monthInvoices = $invoices->filter(function ($invoice) use ($monthStart, $monthEnd) {
                    $invoiceStart = Carbon::parse($invoice->from_date);
                    $invoiceEnd = Carbon::parse($invoice->to_date);

                    // Check if invoice overlaps with month
                    return $invoiceStart->lte($monthEnd) && $invoiceEnd->gte($monthStart);
                });

                $invoiceCount = $monthInvoices->count();
                $isInvoiced = $invoiceCount > 0 ? 'نعم' : 'لا';

                $row[] = $month['name_ar'];
                $row[] = $isInvoiced;
                $row[] = $invoiceCount;
            }

            $results[] = $row;
        }

        return $results;
    }
}
