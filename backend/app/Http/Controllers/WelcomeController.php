<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function redirectTo(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
        ]);

        return redirect()->to($request->url);
    }
}
