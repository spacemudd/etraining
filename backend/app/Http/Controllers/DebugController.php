<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebugController extends Controller
{
    public function loginAsUser($user_id)
    {
        if (auth()->user()->email != 'hello@getshafiq.com') abort(404);
        Auth::loginUsingId($user_id);
        return redirect()->route('dashboard');
    }
}
