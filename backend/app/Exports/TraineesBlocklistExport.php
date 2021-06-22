<?php

namespace App\Exports;

use App\Models\Back\TraineeBlockList;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TraineesBlocklistExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DB::table('trainee_block_lists')->select($this->headings())->get();
    }

    /**
     *
     * @return array|void
     */
    public function headings(): array
    {
        return [
            'name',
            'identity_number',
            'email',
            'phone',
            'phone_additional',
            'reason',
        ];
    }
}
