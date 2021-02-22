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

use App\Models\Back\Payment;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sender_name' => $this->faker->name,
            'sender_bank' => $this->faker->name,
            'amount' => $this->faker->randomNumber(),
            'method' => 'transfer',
            'status' => 0,
            'confirmed_at' => null,
            'confirmed_by' => null,
        ];
    }
}
