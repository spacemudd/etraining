<?php
/**
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace Database\Factories\Back;

use App\Models\Back\CompanyContract;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class CompanyContractFactory extends Factory
{
    use WithFaker;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CompanyContract::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'team_id' => '',
            'company_id' => '',
            'reference_number' => rand(),
            'contract_starts_at' => $this->faker->date(),
            'contract_ends_at' => $this->faker->date(),
            'contract_period_in_months' => 1,
            'auto_renewal' => true,
            'trainees_count' => rand(),
            'trainee_salary' => rand(),
            'instructor_cost' => rand(),
            'company_reimbursement' => rand(),
            'notes' => $this->faker->text,
            'created_by_id' => null,
        ];
    }
}
