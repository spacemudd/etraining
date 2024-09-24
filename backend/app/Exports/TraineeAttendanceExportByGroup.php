<?php

namespace App\Exports;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TraineeAttendanceExportByGroup implements FromCollection,WithHeadings
{
    protected $trainees;
    public function __construct($trainees)
    {
        $this->trainees = $trainees;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
  
    public function collection()
    {
        return collect($this->trainees);
    }

    public function headings(): array
    {
        return ['Trainee Name', 'Present Count', 'Absent Count'];
    }
}
