<?php

namespace Database\Seeders;

use App\Models\Answer;
use Illuminate\Database\Seeder;

class AnswersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $answers = [
            [
                'value' => 'التخطيط', 'is_right' => '1'
            ],
            [
                'value' => 'التخطيط التشغيلي', 'is_right' => '0'
            ],
            [
                'value' => 'الخطة الاستراتيجية', 'is_right' => '0'
            ],
            [
                'value' => 'صح', 'is_right' => '1'
            ],
            [
                'value' => 'خطأ', 'is_right' => '0'
            ],
            [
                'value' => 'التخطيط', 'is_right' => '0'
            ],
            [
                'value' => 'التخطيط التشغيلي', 'is_right' => '1'
            ],
            [
                'value' => 'التخطيط الاستراتيجي', 'is_right' => '0'
            ],
            [
                'value' => 'صح', 'is_right' => '0'
            ],
            [
                'value' => 'خطأ', 'is_right' => '1'
            ],
            [
                'value' => 'التخطيط الاستراتيجي', 'is_right' => '1'
            ],
            [
                'value' => 'التخطيط', 'is_right' => '0'
            ],
            [
                'value' => 'الخطة التشغيلية', 'is_right' => '0'
            ],
            [
                'value' => 'المستوى القطاعي', 'is_right' => '0'
            ],
            [
                'value' => 'المستوى القومي', 'is_right' => '1'
            ],
            [
                'value' => 'مستوى المنظمة', 'is_right' => '0'
            ],
            [
                'value' => 'صح', 'is_right' => '1'
            ],
            [
                'value' => 'خطأ', 'is_right' => '0'
            ],
            [
                'value' => 'صح', 'is_right' => '0'
            ],
            [
                'value' => 'خطأ', 'is_right' => '1'
            ],
            [
                'value' => 'خطأ', 'is_right' => '0'
            ],
            [
                'value' => 'صح', 'is_right' => '1'
            ],
            [
                'value' => 'الفاعلية', 'is_right' => '0'
            ],
            [
                'value' => 'العنصر البشري', 'is_right' => '1'
            ],
            [
                'value' => 'المرونة', 'is_right' => '0'
            ],
        ];
        foreach ($answers as $answer) {
            Answer::create($answer);
        }
    }
}
