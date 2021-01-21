<?php

namespace App\Http\Controllers;

use App\Models\Back\Trainee;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Auth;

class ProfileController extends Controller
{
    // TODO: Make test & refactor.
    
    /**
     *
     * @param $user_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function setupAccount($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }
        $trainee = Trainee::find($user_id);
        if ($trainee) {
            $user = User::find($trainee->user_id);
        }
        Auth::login($user);
        return view('profile.setup-account');
    }

    public function updateAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string|max:255',
        ]);

        $user = User::find(auth()->user()->id);
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
