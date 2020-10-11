<?php

namespace Database\Factories\Back;

use App\Models\Back\Course;
use App\Models\Back\Instructor;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'classroom_count' => $this->faker->numberBetween(10, 25),
            'description' => $this->faker->text,
            'instructor_id' => null,
            'sharable' => false,
            'approval_code' => (string) $this->faker->randomNumber(5),
            'days_duration' => $this->faker->numberBetween(3, 8),
            'hours_duration' => $this->faker->numberBetween(6, 24),
        ];
    }
}
