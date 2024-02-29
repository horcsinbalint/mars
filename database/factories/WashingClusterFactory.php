<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class WashingClusterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        //'name', 'number_of_machines', 'user_maximum_open_reservations', 'open_days_for_reservations'
        return [
            'name' => $this->faker->streetAddress . " floor " . $this->faker->numberBetween(-2,10),
            'number_of_machines' => $this->faker->numberBetween(0,10),
            'user_maximum_open_reservations' => $this->faker->numberBetween(1,10),
            'open_days_for_reservations' => $this->faker->numberBetween(3,30),
            'slot_size' => $this->faker->numberBetween(2,6)*15
        ];
    }
}
