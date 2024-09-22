<?php

namespace Database\Factories;

use App\Models\InboxMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class InboxMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InboxMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'body' => $this->faker->text,
            'read_at' => null,
            'is_system_message' => true,
        ];
    }
}
