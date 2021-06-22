<?php
/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace Database\Factories\Back;

use App\Models\Back\TraineeBlockList;
use Illuminate\Database\Eloquent\Factories\Factory;

class TraineeBlockListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TraineeBlockList::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'identity_number' => $this->faker->randomNumber(),
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'phone_additional' => $this->faker->phoneNumber,
            'reason' => $this->faker->text(100),
        ];
    }
}
