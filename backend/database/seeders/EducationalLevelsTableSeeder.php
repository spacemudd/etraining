<?php

namespace Database\Seeders;

use App\Models\EducationalLevel;
use Illuminate\Database\Seeder;

class EducationalLevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = [
            ['order' => 1, 'name_en' => 'College', 'name_ar' => 'جامعي'],
            ['order' => 2, 'name_en' => 'Diploma', 'name_ar' => 'دبلوم'],
            ['order' => 3, 'name_en' => 'Highschool', 'name_ar' => 'ثانوي'],
            ['order' => 4, 'name_en' => 'Elementary', 'name_ar' => 'متوسط او ابتدائي'],
            ['order' => 5, 'name_en' => 'Other', 'name_ar' => 'آخر'],
        ];

        foreach ($levels as $level) {
            EducationalLevel::create($level);
        }
    }
}
