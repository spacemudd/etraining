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
            '1100609807',
            '1068196292',
            '1120587694',
            '1084219375',
            '1109864072',
            '1085495040',
            '1125581551',
            '1135648952',
            '1096872096',
            '1082265123',
            '1085132767',
            '1208545770',
            '1092696697',
            '1070560782',
            '1103214811',
            '1089546343',
            '1035819265',
            '1085792123',
            '1034847002',
            '1070544984',
            '1097936957',
            '1080214875',
            '1081981845',
            '1075427367',
            '1111931216',
            '1120892581',
            '1133711463',
            '1110099536',
            '1022476111',
            '1120020191',
            '1093525507',
            '1017026434',
            '1109513422',
            '1131485391',
            '1077479796',
            '1103708119',
            '1041880236',
            '1104264831',
            '1029562160',
            '1036292025',
            '1075446292',
            '1105205510',
            '1107417428',
            '١١٠١٦٧٠٣٩٤',
            '1074276989',
            '1023278433',
            '1054988553',
            '1087362685',
            '1111472153',
            '1128464680',
            '1097194896',
            '1090745546',
            '1110380480',
            '1097203903',
            '1084830890',
            '1104708894',
            '1109469799',
            '1123215772',
            '1129354286',
            '1006117509',
            '1088719594',
            '1085850665',
            '1121645343',
            '1093033296',
            '1100424850',
            '1088341076',
            '1014074551',
            '١٠٩١٨٨١١٩١',
            '1119717484',
            '1117416303',
        ];

        $trainees = Trainee::whereIn('identity_number', $idNumbers)
        ->where('zoho_contract_status','completed')
        ->get();
    

        return $trainees->map(function ($trainee) {
            return [
                'name' => $trainee->name,
                'identity_number' => $trainee->identity_number,
                'email' => $trainee->email,
                'phone' => $trainee->phone,
                'company' => $trainee->company->name_ar ?? '-', // لو ما في شركة يظهر "-"
                // 'last_login_at' => optional($trainee->user)->last_login_at ?? '-',
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
            // 'تاريخ آخر دخول',
        ];
    }
}
