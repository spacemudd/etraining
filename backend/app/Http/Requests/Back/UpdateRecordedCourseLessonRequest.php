<?php

declare(strict_types=1);

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecordedCourseLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-recorded-courses');
    }

    public function rules(): array
    {
        return [
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
        ];
    }
}
