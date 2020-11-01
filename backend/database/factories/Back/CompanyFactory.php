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

use App\Models\Back\Company;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    use WithFaker;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'name_ar' => $this->faker('ar_SA')->company,
            'name_en' => $this->faker->company,
            'cr_number' => '1010'.$this->faker->randomNumber(5),
            'contact_number' => $this->faker->phoneNumber,
            'company_rep' => $this->faker('ar_SA')->name,
            'company_rep_mobile' => $this->faker->phoneNumber,
            'address' => $this->faker('ar_SA')->city,
            'email' => $this->faker->companyEmail,
            'team_id' => '',
        ];
    }
}
