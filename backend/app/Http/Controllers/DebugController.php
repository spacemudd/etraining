<?php

namespace App\Http\Controllers;

use App\Models\Back\Trainee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    public function loginAsUser($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            $user_id = Trainee::find($user_id)->user_id;
        }
        //if (auth()->user()->email != 'hello@getshafiq.com' || auth()->user()->email != 'leena@ptc-ksa.com' || auth()->user()->email != 'sara@ptc-ksa.com') abort(404);
        Auth::loginUsingId($user_id);
        return redirect()->route('dashboard');
    }
}
