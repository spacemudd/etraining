<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Seeder;

class MaritalStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $maritalStatuses = [
            ['order' => 1, 'name_en' => 'Single', 'name_ar' => 'غير متزوج'],
            ['order' => 2, 'name_en' => 'Married', 'name_ar' => 'متزوج'],
            ['order' => 3, 'name_en' => 'Divorced', 'name_ar' => 'مطلق'],
            ['order' => 4, 'name_en' => 'Widow/Widower', 'name_ar' => 'ارمل'],
        ];

        foreach ($maritalStatuses as $status) {
            MaritalStatus::firstOrCreate(['name_ar' => $status['name_ar']], [
                'order' => $status['order'],
                'name_ar' => $status['name_ar'],
                'name_en' => $status['name_en'],
            ]);
        }
    }
}
