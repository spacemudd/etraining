<?php
namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TraineeExportRiyadh implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Trainee::where('city_id', 'd4fb0162-81ec-4b17-812a-06c7c4306cb5')
            ->candidates()
            ->get(['identity_number', 'phone','name'])->makeHidden(['show_url']);;
    }

    public function headings(): array
    {
        return [
            'رقم الهوية',
            'رقم الجوال',
            'اسم المتدرب',
        ];
    }
}
