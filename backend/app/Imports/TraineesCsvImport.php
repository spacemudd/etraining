<?php

namespace App\Imports;

use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class TraineesCsvImport implements ToCollection
{
    private $company_id;
    private $trainee_group_id;
    private $trainee_group_name;

    /**
     * TraineesCsvImport constructor.
     *
     * @param $company_id
     * @param $trainee_group_name
     * @param $trainee_group_id string UUID of group. If not provided, the group name will be used to create a new one.
     */
    public function __construct($company_id, $trainee_group_name, $trainee_group_id=null)
    {
        $this->company_id = $company_id;
        $this->trainee_group_name = $trainee_group_name;
        $this->trainee_group_id = $trainee_group_id;
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @return mixed
     * @throws \Throwable
     */
    public function collection(Collection $rows)
    {
        \DB::beginTransaction();

        if ($this->trainee_group_id) {
            $group = TraineeGroup::findOrFail($this->trainee_group_id);
        } else {
            $group = TraineeGroup::firstOrCreate(['name' => trim($this->trainee_group_name)]);
        }

        foreach ($rows as $row) {
            if ($row[0] === 'FullName') {
                continue;
            }

            if (!isset($row[3])) { // The email.
                Log::error('Failed validating for this user: '.$row[0]);
                continue;
            }

            if (! filter_var(($row[3]), FILTER_VALIDATE_EMAIL)) {
                Log::error('Failed validating for this user: '.$row[0]);
                continue;
            }

            if ($trainee = Trainee::where('email', $row[3])->first()) {
                $trainee->team_id = auth()->user()->current_team_id;
                $trainee->company_id = $this->company_id;
                $trainee->trainee_group_id = $group->id;
                $trainee->save();
            } else {
                $trainee = Trainee::make([
                    'team_id' => auth()->user()->current_team_id,
                    'name' => trim($row[0]),
                    'email' => trim($row[3]),
                    'identity_number' => trim($row[1]),
                    'phone' => '966'.trim($row[4]),
                    'phone_additional' => trim($row[5]),
                    'educational_level_id' => $this->getEducationalLevel($row[6]),
                    'city_id' => $this->getCity($row[7]),
                    'marital_status_id' => $this->getMaritalStatus($row[8]),
                    'children_count' => trim($row[9]),
                ]);
                $trainee->team_id = auth()->user()->current_team_id;
                $trainee->company_id = $this->company_id;
                $trainee->trainee_group_id = $group->id;
                $trainee->save();
            }
        }
        \DB::commit();

        return $rows;
    }

    public function getEducationalLevel($string)
    {
        $educational_level_id = null;

        if ($string === 'بكالوريوس') {
            $educational_level_id = EducationalLevel::where('name_en', 'College')->first()->id;
        }

        if ($string === 'ثانويه عامة') {
            $educational_level_id = EducationalLevel::where('name_en', 'Highschool')->first()->id;
        }

        if ($string === 'متوسطة' || $string === 'إبتدائي') {
            $educational_level_id = EducationalLevel::where('name_en', 'Elementary')->first()->id;
        }

        if ($string === 'دبلوم') {
            $educational_level_id = EducationalLevel::where('name_en', 'Diploma')->first()->id;
        }

        return $educational_level_id;
    }

    public function getCity($string)
    {
        return optional(City::where('name_ar', $string)->first())->id;
    }

    public function getMaritalStatus($string)
    {
        if ($string === 'اعزب' || $string === 'أعزب') {
            return MaritalStatus::where('name_en', 'Single')->first()->id;
        }

        if ($string === 'متزوج') {
            return MaritalStatus::where('name_en', 'Married')->first()->id;
        }
    }
}
