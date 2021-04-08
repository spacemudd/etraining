<?php

namespace App\Exports\Back;

use App\Models\Back\Trainee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanyTraineeExport implements FromCollection, WithHeadings
{

    public function __construct($company_id) {
        $this->company_id = $company_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() {
        return Trainee::where('company_id', $this->company_id)->get();
    }

    public function headings(): array {
        return [
            'id',
            'team_id',
            'user_id',
            'name',
            'email',
            'identity_number',
            'phone',
            'phone_additional',
            'birthday',
            'educational_level_id',
            'city_id',
            'marital_status_id',
            'children_count',
            'company_id',
            'deleted_at',
            'created_at',
            'updated_at',
            'instructor_id',
            'status',
            'approved_by_id',
            'approved_at',
            'deleted_remark',
            'skip_uploading_id',
        ];
    }


}
