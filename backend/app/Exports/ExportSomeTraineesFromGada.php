<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class ExportSomeTraineesFromGada implements FromCollection, WithHeadings
{
    public function collection()
    {

        $date=Carbon::parse('2025-01-01');
        
        
        
        $trainees = Trainee::whereHas('user', function ($q) use ($date) {
            $q->where('last_login_at', '<', $date);
        })
        ->where(function ($q) {
            $q->whereNotNull('company_id')
              ->orWhereNotNull('trainee_group_id');
        })
        ->get();
        



        return $trainees->map(function($trainee){
            return [
                'name' => $trainee->name,
                'identity_number' => $trainee->identity_number,
                'email'=> $trainee->email,
                'phone' => $trainee->phone,
                'company' => optional($trainee->company)->name_ar,
                'trainee_group' => optional($trainee->trainee_group)->name,
                'last_login_at'=>$trainee->user->last_login_at,
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
            'الشعبة',
            'تاريخ آخر دخول',

        ];
    }
}

