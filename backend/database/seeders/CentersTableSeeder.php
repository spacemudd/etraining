<?php

namespace Database\Seeders;

use App\Models\Center;
use Illuminate\Database\Seeder;

class CentersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $centers = collect([
            "مركز احترافية التدريب",
            "مركز جسر",
            "مركز جسارة",
        ]);

        $centers->each(function($city) {
            Center::firstOrCreate(['name_ar' => $city], [
                'name' => $city,
                'name_ar' => $city,
            ]);
        });
    }
}
