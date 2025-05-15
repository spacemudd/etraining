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
            '1123446377',
            '1142458262',
            '1095636765',
            '1084534641',
            '1092637311',
            '1075920999',
            '1095894778',
            '1089888000',
            '1044983219',
            '1106540030',
            '1035819265',
            '1034847002',
            '1075427367',
            '1120892581',
            '1118199551',
            '1113659666',
            '1105681561',
            '1062231285',
            '1114422296',
            '1059001626',
            '1037113105',
            '1073288381',
            '1132121516',
            '١٠٣٤٥٩٠٦٣٦',
            '1115288340',
            '1086180252',
            '1089743882',
            '1075765246',
            '1129974836',
            '1080216219',
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

        $trainees = Trainee::onlyTrashed()->whereIn('identity_number',$idNumbers)
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
