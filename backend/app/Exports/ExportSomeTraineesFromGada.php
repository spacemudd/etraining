<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use App\Models\Back\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ExportSomeTraineesFromGada implements FromCollection, WithHeadings
{
    public function collection()
    {
        $invoiceIds=[
            'fde280f6-2329-4aba-98f5-314a30f9245b',  
            '007efc69-46ca-44c8-ab03-c12dad6eb802',  
            '6d23e452-4ee3-43f7-99a0-c49e7a978d06',  
            '78be29b2-6e2f-4512-a2a7-e9d6d2220256',  
            '6c1e96f8-50d6-4dbb-874f-448abd2586b6',  
            '69f4dde7-3a11-4607-94ab-69e922da2dfb',  
            '2dfb5e3e-6da6-40b4-a06a-5e23f796893e',  
            'c165988f-dea1-4598-90f5-95e49f165246',  
            '1f4b1653-0888-4003-860b-aee799f74651',  
            '1734667c-1ce9-4c34-9cd0-d5eded52c699',  
            '07a02794-97db-4fdc-8dff-30d67652c6f0',  
            '7d241618-4923-4dd0-a591-6b5748df905c',  
            '6c1e96f8-50d6-4dbb-874f-448abd2586b6',  
            'a6218b39-f481-4d38-8ace-e2769a64ed15',  
            'cf488c8f-995a-4265-8a2a-6c3e1d11d889',  
            '87d4daf3-9e2d-42cf-a45e-6208da029152',  
            'cc106952-b79e-4db1-8b72-0fe6da036fe6',  
            'c1e31042-927e-45f9-a9c4-60d18c4676e8',  
            '9ee2c464-dec9-49e6-b1a0-c5a626ce010f',  
            '077f7f06-d7bc-4fd9-89f1-0535a24c846a',  
            '41530c04-4bbd-4d32-a9b1-061edded0815',  
            '1734667c-1ce9-4c34-9cd0-d5eded52c699',  
            '007efc69-46ca-44c8-ab03-c12dad6eb802',  
            '007efc69-46ca-44c8-ab03-c12dad6eb802',  
            '1734667c-1ce9-4c34-9cd0-d5eded52c699',  
            '1f4b1653-0888-4003-860b-aee799f74651',  
            '6d23e452-4ee3-43f7-99a0-c49e7a978d06',  
            '07a02794-97db-4fdc-8dff-30d67652c6f0',  
            '007efc69-46ca-44c8-ab03-c12dad6eb802',  
            'c165988f-dea1-4598-90f5-95e49f165246',

        ];

        $trainees = Trainee::whereIn('id', function ($query) use ($invoiceIds) {
            $query->select('trainee_id')
                ->from('invoices')
                ->whereIn('id', $invoiceIds);
        })->get();
            

        return $trainees->map(function ($trainee) {
            return [
                'name' => $trainee->name_ar,
                'phone' => $trainee->phone,
                'company' => $trainee->company->name_ar,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'الشركة',
            'الجوال',
            'الإسم',
        ];
    }
}
