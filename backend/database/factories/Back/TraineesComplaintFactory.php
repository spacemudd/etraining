<?php

namespace Database\Factories\Back;

use App\Models\Back\TraineesComplaint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TraineesComplaintFactory extends Factory
{
    protected $model = TraineesComplaint::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'number' => '123',
            'contact_way' => 'phone',
            'complaints' => $this->faker->text(100),
        ];
    }
}
