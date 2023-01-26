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
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => '….. هو عملية رسم الأهداف التي يراد التوصل إليها خلال فترة زمنية معينة:',
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => 'التخطيط هو خطوات عمل معينة ومحددة ومركبة وفق دراسة علمية سليمة مبنية على حقائق وتقديرات مدروسة:',
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => '… يصف مراحل وشروط النجاح ويشرح كيفية إدخال خطة إستراتيجية ما:',
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => 'الخطة التشغيلية ليست أساس طلب ميزانية التشغيل السنوية:',
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => '…. هو مـنهج نظـامي يستشـرف آفـاق المسـتقبليات التربويـة والتعليميـة المحتملـة والممكنـة:',
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => '…. هو المستوى الذي يشير إلى خطط التنمية الشاملة التي تتم على مستوى الدولة ككل:',
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => 'من فوائد التخطيط في الحياة الإدارية التنبؤ بالمستقبل:',
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => 'من مبادئ التخطيط في الحياة الإدارية عدم انعكاس التخطيط على الوظائف الإدارية الأخرى:',
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => 'التخطيط في الحياة الإدارية يقلل من الأخطاء بجميع أشكالها وعلى كافة المستويات:',
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5',
                'quiz_id' => '5',
                'description' => 'من أدوات التخطيط:',
            ],
        ];
        foreach ($questions as $question) {
            Question::create($question);
        }
    }
}
