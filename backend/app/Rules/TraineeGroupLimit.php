<?php

namespace App\Rules;

use App\Models\Back\TraineeGroup;
use Illuminate\Contracts\Validation\Rule;

class TraineeGroupLimit implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value) {
            $group = TraineeGroup::where('name', $value)->first();
            return $group->trainees()->count() < 1000;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('words.group-name').' - '.'لا يمكنك الاضافية الى هذه المجموعة لأنها ممتلئة';
    }
}
