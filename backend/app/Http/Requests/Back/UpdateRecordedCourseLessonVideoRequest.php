<?php

declare(strict_types=1);

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateRecordedCourseLessonVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-recorded-courses');
    }

    public function rules(): array
    {
        $maxKb = (int) (config('media-library.max_file_size', 524288000) / 1024);

        return [
            'video' => [
                'nullable',
                'file',
                'mimetypes:video/mp4,video/webm,video/quicktime',
                'max:'.$maxKb,
            ],
            'upload_token' => ['nullable', 'uuid'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hasFile = $this->hasFile('video');
            $hasToken = filled($this->input('upload_token'));
            if (! $hasFile && ! $hasToken) {
                $validator->errors()->add('video', __('words.recorded-course-video-required'));
            }
            if ($hasFile && $hasToken) {
                $validator->errors()->add('video', __('words.recorded-course-video-upload-one-method'));
            }
        });
    }
}
