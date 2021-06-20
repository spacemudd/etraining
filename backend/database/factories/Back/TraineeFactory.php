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

use App\Models\Back\Trainee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class TraineeFactory extends Factory
{
    use WithFaker;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Trainee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker('ar_SA')->name('female'),
            'identity_number' => $this->faker('ar_SA')->idNumber(),
            'phone' => $this->faker('ar_SA')->phoneNumber,
            'phone_additional' => $this->faker('ar_SA')->phoneNumber,
            'birthday' => $this->faker->date('Y-m-d', '-24 years'),
            'educational_level_id' => null,
            'city_id' => null,
            'marital_status_id' => null,
            'children_count' => $this->faker->randomDigit,
            'company_id' => null,
            'email' => $this->faker->email,
        ];
    }
}
