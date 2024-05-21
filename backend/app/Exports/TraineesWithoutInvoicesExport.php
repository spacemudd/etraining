<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use App\Models\Back\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TraineesWithoutInvoicesExport implements FromArray, WithHeadings, WithMapping  ,WithTitle,ShouldAutoSize
{
    public $data;


    function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'trainees-without-invoices';
    }


    public function headings(): array
    {
        return [
            'الاسم',
            'اسم الشركة',
            'الايميل',
            'رقم الهوية',
            'رقم الموبايل',
            'تاريخ الانشاء',
            'تاريخ التعديل'
        ];
    }

    public function map($trainee): array
    {
        return [
            $trainee['name'],
            optional($trainee['company'])['name_ar'],
            $trainee['email'],
            $trainee['identity_number'],
            $trainee['phone'],
            $trainee['created_at'],
            $trainee['updated_at'],
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $startDate = $this->data['date_from'];
        $endDate = $this->data['date_to'];

        $traineesWithoutInvoices = Trainee::whereDoesntHave('invoices', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('from_date', [$startDate, $endDate]);
        })->toBase();

        return (array) $traineesWithoutInvoices;
    }
}
