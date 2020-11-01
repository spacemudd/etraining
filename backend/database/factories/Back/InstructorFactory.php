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

use App\Models\Back\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;

class InstructorFactory extends Factory
{
    use WithFaker;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Instructor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'reference_number' => $this->faker->randomNumber(),
            'name' => $name = $this->faker('ar_SA')->name,
            'identity_number' => $this->faker('ar_SA')->nationalIdNumber,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'twitter_link' => 'https://twitter.com/'.str_slug($name),
            'city_id' => null,
            'user_id' => null,
        ];
    }
}
