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
                'question_id' => '1', 'value' => 'التخطيط', 'is_correct' => '1'
            ],
            [
                'question_id' => '1', 'value' => 'التخطيط التشغيلي', 'is_correct' => '0'
            ],
            [
                'question_id' => '1', 'value' => 'الخطة الاستراتيجية', 'is_correct' => '0'
            ],
            [
                'question_id' => '2', 'value' => 'صح', 'is_correct' => '1'
            ],
            [
                'question_id' => '2', 'value' => 'خطأ', 'is_correct' => '0'
            ],
            [
                'question_id' => '3', 'value' => 'التخطيط', 'is_correct' => '0'
            ],
            [
                'question_id' => '3', 'value' => 'التخطيط التشغيلي', 'is_correct' => '1'
            ],
            [
                'question_id' => '3', 'value' => 'التخطيط الاستراتيجي', 'is_correct' => '0'
            ],
            [
                'question_id' => '4', 'value' => 'صح', 'is_correct' => '0'
            ],
            [
                'question_id' => '4', 'value' => 'خطأ', 'is_correct' => '1'
            ],
            [
                'question_id' => '5', 'value' => 'التخطيط الاستراتيجي', 'is_correct' => '1'
            ],
            [
                'question_id' => '5', 'value' => 'التخطيط', 'is_correct' => '0'
            ],
            [
                'question_id' => '5', 'value' => 'الخطة التشغيلية', 'is_correct' => '0'
            ],
            [
                'question_id' => '6', 'value' => 'المستوى القطاعي', 'is_correct' => '0'
            ],
            [
                'question_id' => '6', 'value' => 'المستوى القومي', 'is_correct' => '1'
            ],
            [
                'question_id' => '6', 'value' => 'مستوى المنظمة', 'is_correct' => '0'
            ],
            [
                'question_id' => '7', 'value' => 'صح', 'is_correct' => '1'
            ],
            [
                'question_id' => '7', 'value' => 'خطأ', 'is_correct' => '0'
            ],
            [
                'question_id' => '8', 'value' => 'صح', 'is_correct' => '0'
            ],
            [
                'question_id' => '8', 'value' => 'خطأ', 'is_correct' => '1'
            ],
            [
                'question_id' => '9', 'value' => 'خطأ', 'is_correct' => '0'
            ],
            [
                'question_id' => '9', 'value' => 'صح', 'is_correct' => '1'
            ],
            [
                'question_id' => '10', 'value' => 'الفاعلية', 'is_correct' => '0'
            ],
            [
                'question_id' => '10', 'value' => 'العنصر البشري', 'is_correct' => '1'
            ],
            [
                'question_id' => '10', 'value' => 'المرونة', 'is_correct' => '0'
            ],
        ];
        foreach ($answers as $answer) {
            Answer::create($answer);
        }
    }
}
