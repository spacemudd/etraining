<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EnglishNameController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'english_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/', // Only English letters and spaces
            ]
        ], [
            'english_name.required' => 'الاسم الإنجليزي مطلوب',
            'english_name.string' => 'الاسم الإنجليزي يجب أن يكون نص',
            'english_name.max' => 'الاسم الإنجليزي يجب أن لا يتجاوز 255 حرف',
            'english_name.regex' => 'الاسم الإنجليزي يجب أن يحتوي على أحرف إنجليزية فقط'
        ]);

        $user = Auth::user();
        $trainee = Trainee::where('user_id', $user->id)->first();

        if (!$trainee) {
            return response()->json([
                'message' => 'المتدرب غير موجود'
            ], 404);
        }

        $trainee->update([
            'english_name' => $request->english_name
        ]);

        return response()->json([
            'message' => 'تم حفظ الاسم الإنجليزي بنجاح',
            'english_name' => $request->english_name
        ]);
    }
} 