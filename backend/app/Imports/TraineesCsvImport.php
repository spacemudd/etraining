<?php

namespace App\Imports;

use App\Models\Back\Company;
use App\Models\Back\Trainee;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TraineesCsvImport implements ToCollection
{
    /**
     * @param \Illuminate\Support\Collection $rows
     * @return mixed
     */
    public function collection(Collection $rows)
    {
        \DB::beginTransaction();
        $company_id = Company::where('name_en', 'PTC')->firstOrFail()->id;

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
            $trainee->company_id = $company_id;
            $trainee->save();
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
