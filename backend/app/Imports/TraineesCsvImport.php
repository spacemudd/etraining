<?php

namespace App\Imports;

use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
use Illuminate\Support\Collection;
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
            $group = TraineeGroup::firstOrCreate(['name' => $this->trainee_group_name]);
        }

        foreach ($rows as $row) {
            if ($row[0] === 'FullName') {
                continue;
            }

            if (!isset($row[3])) { // The email.
                continue;
            }

            $trainee = Trainee::make([
                'team_id' => auth()->user()->current_team_id,
                'name' => $row[0],
                'email' => $row[3],
                'identity_number' => $row[1],
                'phone' => '966'.$row[4],
                'phone_additional' => $row[5],
                'educational_level_id' => $this->getEducationalLevel($row[6]),
                'city_id' => $this->getCity($row[7]),
                'marital_status_id' => $this->getMaritalStatus($row[8]),
                'children_count' => $row[9],
            ]);

            $trainee->team_id = auth()->user()->current_team_id;
            $trainee->company_id = $this->company_id;
            $trainee->save();

            $group->trainees()->attach([$trainee->id]);
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
        return City::where('name_ar', $string)->first()->id;
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
