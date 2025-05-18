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
        $idNumbers=[
           '1133434629',
           '1095233506',
           '1092637311',
           '1118384880',
           '1079775498',
           '1044671715',
           '1104582851',
           '1092628005',
           '1120560675',
           '1085760823',
           '1101076550',
           '1095894778',
           '1106540030',
           '1035819265',
           '1034847002',
           '1075427367',
           '1120892581',
           '1113659666',
           '1105681561',
           '1062231285',
           '1132121516',
           '١٠٣٤٥٩٠٦٣٦',
           '1080216219',
           '1118228863',
           '1098159989',
           '1070429996',
           '1024845404',
           '1060700729',
           '1122295627',
           '1097815821',
           '1108944180',
           '1097727117',
           '١٠٠١٩٤٩٨١٥',
        ];

        $trainees = Trainee::withTrashed()->whereIn('identity_number',$idNumbers)
        ->where('zoho_contract_status','completed')
        ->get();
    

        return $trainees->map(function ($trainee) {
            return [
                'name' => $trainee->name,
                'identity_number' => $trainee->identity_number,
                'email' => $trainee->email,
                'phone' => $trainee->phone,
                'company' => $trainee->company->name_ar ?? '-',
                'status' => $trainee->zoho_contract_status,
          
            ];
        });
    }

    public function headings(): array
    {
        return [
            'الإسم',
            'الهوية',
            'الإيميل',
            'الجوال',
            'الشركة',
            'حالة العقد',
        ];
    }
}
