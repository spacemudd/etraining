<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportSomeTraineesFromGada implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return Trainee::query()
            ->onlyTrashed()
            ->where('deleted_remark', 'غير مسجلة في الشركة')
            ->select(['name', 'identity_number', 'phone', 'deleted_remark', 'deleted_at'])
            ->orderBy('name');
    }

    /**
     * @param  Trainee  $row
     */
    public function map($row): array
    {
        $name = (string) ($row->name ?? '');
        $identityNumber = (string) ($row->identity_number ?? '');

        $phone = (string) ($row->phone ?? '');
        $phoneAsText = $phone !== '' ? '="' . $phone . '"' : '';

        return [$name, $identityNumber, $phoneAsText];
    }

    public function headings(): array
    {
        return ['الاسم', 'الهوية', 'رقم الجوال'];
    }
}
