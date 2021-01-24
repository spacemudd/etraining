<?php

namespace App\Exports\Back;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;

class TraineeExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Trainee::all();
    }
}
