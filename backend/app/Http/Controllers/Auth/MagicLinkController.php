<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\MagicLinkNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class MagicLinkController extends Controller
{
    // Send magic link to email
    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $user = User::where('email', $request->email)->firstOrFail();
        $token = URL::temporarySignedRoute(
            'login.magic-link.consume',
            now()->addMinutes(10),
            ['token' => sha1($user->email . '|' . $user->id . '|' . now()->timestamp)]
        );
        Notification::route('mail', $user->email)
            ->notify(new MagicLinkNotification($token));
        return redirect()->route('login.magic-link.sent');
    }

    // Consume magic link and log in
    public function login(Request $request, $token)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'This magic link is invalid or has expired.');
        }
        // Find user by email in the signed URL
        $email = $request->query('email');
        $user = User::where('email', $email)->first();
        if (! $user) {
            abort(404, 'User not found.');
        }
        Auth::login($user);
        return redirect()->route('dashboard');
    }

    public function sent()
    {
        return view('auth.magic-link-sent');
    }
} 