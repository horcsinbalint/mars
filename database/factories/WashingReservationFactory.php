<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class WashingReservationFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $cluster = \App\Models\WashingCluster::get()->random();
        $request_time = $this->faker->dateTimeBetween("0 days", $cluster->open_days_for_reservations . " days");
        $starts_on = $cluster->getSlotStart($request_time);
        $ends_on = $cluster->getSlotEnd($request_time);

        return [
            'user_id' => \App\Models\User::where('verified', true)->get()->random()->id,
            'cluster_id' => $cluster->id,
            'starts_on' => $starts_on,
            'ends_on' => $ends_on
        ];
    }
}

