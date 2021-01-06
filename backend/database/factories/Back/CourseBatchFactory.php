<?php

namespace Database\Factories\Back;

use App\Models\Back\CourseBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseBatchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseBatch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'trainee_group_id' => null,
            'course_id' => null,
            'starts_at' => $this->faker->date(),
            'ends_at' => $this->faker->date(),
            'location_at' => $this->faker->randomElement(['online', 'Riyadh', 'Damamm']),
        ];
    }
}
