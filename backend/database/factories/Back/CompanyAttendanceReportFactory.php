<?php

namespace Database\Factories\Back;

use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyAttendanceReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanyAttendanceReport::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date_from = now();

        return [
            'number' => rand(),
            'date_from' => $date_from->startOfMonth(),
            'date_to' => $date_from->endOfMonth(),
            'status' => CompanyAttendanceReport::STATUS_APPROVED,
            'to_emails' => $this->faker->companyEmail,
        ];
    }
}
