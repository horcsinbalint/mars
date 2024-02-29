<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\WashingCluster;
use App\Models\WashingReservation;

class WashingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WashingCluster::factory()->count(2)->create();
        WashingReservation::factory()->count(100)->create();
    }
}
