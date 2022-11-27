<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = [
            [
                'quiz_id' => '5',
                'answer_id' => '1',
                'description' => '….. هو عملية رسم الأهداف التي يراد التوصل إليها خلال فترة زمنية معينة:',
            ],
            [
                'quiz_id' => '5',
                'answer_id' => '4',
                'description' => 'التخطيط هو خطوات عمل معينة ومحددة ومركبة وفق دراسة علمية سليمة مبنية على حقائق وتقديرات مدروسة:
',
            ],
            [
                'quiz_id' => '5',
                'answer_id' => '7',
                'description' => '… يصف مراحل وشروط النجاح ويشرح كيفية إدخال خطة إستراتيجية ما:',
            ],
            [
                'quiz_id' => '5',
                'answer_id' => '10',
                'description' => 'الخطة التشغيلية ليست أساس طلب ميزانية التشغيل السنوية:',
            ],
            [
                'quiz_id' => '5',
                'answer_id' => '11',
                'description' => '…. هو مـنهج نظـامي يستشـرف آفـاق المسـتقبليات التربويـة والتعليميـة المحتملـة والممكنـة:',
            ],
            [
                'quiz_id' => '5',
                'answer_id' => '15',
                'description' => '…. هو المستوى الذي يشير إلى خطط التنمية الشاملة التي تتم على مستوى الدولة ككل:',
            ],
            [
                'quiz_id' => '5',
                'answer_id' => '17',
                'description' => 'من فوائد التخطيط في الحياة الإدارية التنبؤ بالمستقبل:',
            ],
            [
                'quiz_id' => '5',
                'answer_id' => '20',
                'description' => 'من مبادئ التخطيط في الحياة الإدارية عدم انعكاس التخطيط على الوظائف الإدارية الأخرى:',
            ],
            [
                'quiz_id' => '5',
                'answer_id' => '22',
                'description' => 'التخطيط في الحياة الإدارية يقلل من الأخطاء بجميع أشكالها وعلى كافة المستويات:',
            ],
            [
                'quiz_id' => '5',
                'answer_id' => '24',
                'description' => 'من أدوات التخطيط:',
            ],
        ];
        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}
