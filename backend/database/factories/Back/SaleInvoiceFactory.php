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

use App\Models\Back\SaleInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleInvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaleInvoice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'issued_at' => $this->faker->dateTimeBetween('-1 year'),
            'sub_total' => $this->faker->randomNumber(),
            'tax_total' => $this->faker->randomNumber(),
            'grand_total' => $this->faker->randomNumber(),
        ];
    }
}
