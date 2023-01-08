<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizzesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quizzes = [
            [
                'course_id' => '26b7da2d-ec08-463a-8b4a-2d2accb6cea9', 'name_ar' => 'اختبار الذكاء الاصطناعي'
            ],
            [
                'course_id' => '53f0e7be-1325-4bc9-a2c9-4a2c967f15ae', 'name_ar' => 'اختبار امن سيبراني'
            ],
            [
                'course_id' => '774fdefa-de2a-4c92-9f1b-76133534ab45', 'name_ar' => 'مطور مواقع ويب اختبار '
            ],
            [
                'course_id' => 'b2402e58-599b-41aa-b2f1-52bcb3077c16', 'name_ar' => 'تطوير لارافيل اختبار '
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'name_ar' => 'تقنية معلومات اختبار '
            ],
        ];

        foreach ($quizzes as $quiz) {
            Quiz::create($quiz);
        }
    }
}
