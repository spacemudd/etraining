<?php

declare(strict_types=1);

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecordedCourseDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-recorded-courses');
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
