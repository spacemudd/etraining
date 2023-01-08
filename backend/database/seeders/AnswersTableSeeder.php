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
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '21', 'value' => 'التخطيط', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '21', 'value' => 'التخطيط التشغيلي', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '21', 'value' => 'الخطة الاستراتيجية', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '22', 'value' => 'صح', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '22', 'value' => 'خطأ', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '23', 'value' => 'التخطيط', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '23', 'value' => 'التخطيط التشغيلي', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '23', 'value' => 'التخطيط الاستراتيجي', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '24', 'value' => 'صح', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '24', 'value' => 'خطأ', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '25', 'value' => 'التخطيط الاستراتيجي', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '25', 'value' => 'التخطيط', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '25', 'value' => 'الخطة التشغيلية', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '26', 'value' => 'المستوى القطاعي', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '26', 'value' => 'المستوى القومي', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '26', 'value' => 'مستوى المنظمة', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '27', 'value' => 'صح', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '27', 'value' => 'خطأ', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '28', 'value' => 'صح', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '28', 'value' => 'خطأ', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '29', 'value' => 'خطأ', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '29', 'value' => 'صح', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '30', 'value' => 'الفاعلية', 'is_correct' => '0'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '30', 'value' => 'العنصر البشري', 'is_correct' => '1'
            ],
            [
                'course_id' => 'abedda89-eb23-42ec-9a03-a19097523eb5', 'question_id' => '30', 'value' => 'المرونة', 'is_correct' => '0'
            ],
        ];
        foreach ($answers as $answer) {
            Answer::create($answer);
        }
    }
}
