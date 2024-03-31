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
        $centers = [
            ['name' => 'مركز احترافية المدرب للتدريب', 'name_ar' => 'مركز احترافية المدرب للتدريب', 'domain_name' => 'app.ptc-ksa.net'],
            ['name' => 'مركز جسر', 'name_ar' => 'مركز جسر', 'domain_name' => 'jisr-ksa.com'],
            ['name' => 'مركز جسارة', 'name_ar' => 'مركز جسارة', 'domain_name' => 'jasarah-ksa.com'],
            ];

        foreach ($centers as $center) {
            Center::firstOrCreate(['name_ar' => $center['name_ar']], [
                'name' => $center['name'],
                'name_ar' => $center['name_ar'],
                'domain_name' => $center['domain_name'],
            ]);
        }
    }
}
