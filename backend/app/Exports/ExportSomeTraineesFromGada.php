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
             '1142458262',
             '1131569830',
             '1114128562',
             '1067218402',
             '1083553048',
             '1115636043',
             '1113608028',
             '١١٠٩٥٩٧٣٥٩',
             '1120806656',
             '1101101168',
             '1049917287',
             '1091043289',
             '1093569026',
             '1072284464',
             '1083294965',
             '1114948100',
             '1121228397',
             '1098806332',
             '1127387817',
             '1126306453',
             '1085592796',
             '1087953590',
             '1098899998',
             '1108934819',
             '1090327998',
             '1084896800',
             '1084456753',
             '1094485784',
             '1123372409',
             '1037113105',
             '1057171561',
             '1043213469',
             '1118067063',
             '1057313338',
             '1129974836',
             '1073286716',
             '1020634984',
             '1095233506',
             '1106540030',
             '1120892581',
             '1098159989',
             '1070429996',
             '1060700729',
             '1122295627',
             '1092637311',
             '١٠٣٤٥٩٠٦٣٦',
             '1118402500',
             '1106987488',
             '1092559911',
             '1098389750',
             '1084419330',
             '1090786045',
             '1097372179',

        ];

        $trainees = Trainee::withTrashed()->whereIn('identity_number',$idNumbers)
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
