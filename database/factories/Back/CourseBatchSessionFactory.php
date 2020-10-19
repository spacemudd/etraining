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

use App\Models\Back\CourseBatchSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseBatchSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseBatchSession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'team_id' => '',
            'course_id' => '',
            'course_batch_id' => '',
            'starts_at' => $this->faker->date(),
            'ends_at' => $this->faker->date(),
        ];
    }
}
