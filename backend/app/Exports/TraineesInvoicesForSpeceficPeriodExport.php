<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use App\Models\Back\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;


class TraineesInvoicesForSpeceficPeriodExport implements FromCollection, WithHeadings, WithMapping  ,WithTitle,ShouldAutoSize
{
    public $data;
 

    function __construct($data) {
       $this->data=$data;
    }

    public function title(): string
    {
        return 'trainees-invoices-for-specefic-period';
    }


    public function headings(): array
    {
        return [
             'Name',
             'Company',
             'Email',
             'Id',
             'Invoice Count',
             'Invoices Value',
            // 'اسم الجروب',
            // 'الايميل',
            // 'رقم الهوية',
            // 'رقم الموبايل',
            // 'تاريخ الانشاء',
            // 'تاريخ التعديل'
        ];
    }

    public function map($trainee): array
    {
        return [
              $trainee->name,
              $trainee->company_name,
              $trainee->email,
              $trainee->identity_number,
              $trainee->invoice_count,
              $trainee->total_grand_total,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $startDate=$this->data['date_from'];
        $endDate=$this->data['date_to'];
        $companyId=$this->data['company_id'];

        // $traineesWithoutInvoices= Trainee::WhereDoesntHave('invoices', function ($query) use ($startDate, $endDate) {
        //     $query->whereBetween('from_date', [$startDate, $endDate]);
        // })->get();
        //  return $traineesWithoutInvoices;



        // $trainees = Trainee::whereHas('invoices', function ($query) use ($companyId, $startDate, $endDate) {
        //     $query->where('company_id', $companyId)
        //           ->whereBetween('from_date',[$startDate, $endDate]);
        // })->get();
    
        // return $trainees;

        $trainees = DB::table('trainees')
        ->join('companies', 'trainees.company_id', '=', 'companies.id')
        ->leftJoin('invoices', function($join) use ($startDate, $endDate) {
        $join->on('trainees.id', '=', 'invoices.trainee_id')
        ->whereBetween('invoices.from_date', [$startDate, $endDate]);
    })
    ->select(
        'trainees.identity_number',
        'trainees.name',
        'trainees.email',
        'companies.name_ar as company_name',
        DB::raw('IFNULL(COUNT(invoices.id), 0) as invoice_count'),
        DB::raw('IFNULL(SUM(invoices.grand_total), 0) as total_grand_total')
    )
    ->where('trainees.company_id', $companyId)
    ->groupBy('trainees.id', 'trainees.name', 'trainees.email', 'companies.name_ar')
    ->get();

    // dd($trainees);
    return $trainees;
 }


}
