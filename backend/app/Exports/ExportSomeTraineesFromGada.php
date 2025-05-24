<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportSomeTraineesFromGada implements FromCollection, WithHeadings
{
    public function collection()
    {
        $idNumbers = [
            'ftwnalwdh@gmail.com', 'boon3455@icloud.com', 'zzazzah6@gmail.com',
            'asmaanaaseer@gmail.com', 'aryam.aqel@icloud.com', 'manalsaleh247@gmail.com',
            'wafaali14200@gmail.com', 'woood1413@gmail.com', 'zoz_xi21@icloud.com',
            'nuouf.1995@gmail.com', 'sa7033344@gmail.com', 'nura_233@hotmail.com',
            'SH.9009.sh@hotmail.com', 'rmosh_8040@hotmail.com', 'fatma2011t@hotmail.com',
            'hatoongroosh@hotmail.com', 'flwa1005@gmail.com', 'famutairi4@gmail.com',
            'shahad0alenazi@gmail.com', 'najd.tttn@icloud.com', 'abdullahsee11@icloud.com',
            'ran817062@gmail.com', 'mm8994724@gmail.com', 'al7or.al@hotmail.com',
            'noof-188@hotmail.com', 'zmmmuttawa@hotmail.com', 'nouf1406m@hotmail.com',
            'famyymf@icloud.com', 'dahlia28qq@gmail.com', 'salwaalkhthmy@gmail.com',
            'fahadalshareef22@gmail.com', 'samaheralotaibi75@gmail.com', 'memalone1991@gmail.com',
            'sarah541160340@gmail.com', 'saha.roqi@gmail.com', 'anwaralshehri889@gmail.com',
            'hnood-455@hotmail.com', 'sa4419488@gmail.com', 'razansu74@gmail.com',
            'fatennashi9@gmail.com', 'Ss0564442186@gmail.com', 'm-albakhit@hotmail.com',
            'ahuod.almutiri@hotmail.com', 's.m.alsubaie0@gmail.com', 'sarazabn11@gamil.com',
            'nawal.mtr@hotmail.com', 'layla1406.1988@gmail.com', 'moonytala123@gmail.com',
            'zooshaalroqe@gmail.com', 'N_oori92@icloud.com', 'shahdas.1@hotmail.com',
            'Maha_Mutlaq@Outlook.sa', 'hor.511_5@icloud.com', 'ryoofalsubaie33@gmail.com',
            'si9xdr@gmail.com', 'roor1044@gmail.com', 'os434003178@gmail.com',
            'rahafzaed34@gmail.com', 'Saamm061@gmail.com', 'nouf__fahad1991@hotmail.com',
            'ashwaqalboogmi@gmail.com', 'arym80654@gmail.com', 'ar_alqarni@hotmail.com',
            'asaiill5455@gmail.com', 'najlaaq10@gmail.com', 'shahad2090sho@gmail.com',
            'faihan.n20@hotmail.com', 'walaert123qw@gmail.com', 'Sa.alharthi12345678@gmail.com',
            'rahem199p@gmail.com', 'na6d90@gmail.com', 'amoooorh_101@hotmail.com',
            'miss_jojo_cool@hotmail.com', 'mirahalshammri@hotmail.com', 'haifa6677ss@gmail.com',
            'albgmimozinah@gmail.com', 'Uliiilimin@gmail.com', 'aas2016ma@gmail.com',
            'nouf1788fahad@icloud.com', 'ghaidaalotaibi414@gmail.com', 'gpsax2@icloud.com',
            'mariamf.92@hotmail.com', 'fmf-1111@hotmail.com', 'norah.alsubaie1234@gmail.com',
            'haaaaf21@gmail.com', 'aisha.fahadfr@outlook.com', 'b.s.a.alhrbi@gmail.com',
            'afrah-alwasidi@hotmail.com', 'sondos14222@jmail.com', 'asmaalhajrii97@gmail.com',
            'bsomaa.z@gmail.com', 'Szz009@hotmail.com', 'Ghalaalsaif40@gmail.com',
            'amj.turk@gmail.com', 'muneeramarta@gmail.com', 'xrem50@icloud.com',
            'Realanazi88@gmail.com', 'turkia198t@gmail.com', 'aiphoneksa@gmail.com',
            'adelxx525212@gmail.com', 'sarah.alotaibi220@gmail.com', 'cutygado@gmail.com',
            'Amani223344@hotmail.com', 'lllighttt24@gmail.com', 'fsarey@gmail.com'
        ];

        // Using the whereIn method and ensuring the order is preserved
        $trainees = Trainee::withTrashed()->whereIn('email', $idNumbers)->get();

        // Map the result to maintain the order of the idNumbers array
        return $trainees->sortBy(function($trainee) use ($idNumbers) {
            return array_search($trainee->email, $idNumbers);
        })->map(function ($trainee) {
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
