<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function changeLanguage($language)
    {
        session()->put('locale', $language);
        return redirect()->back();
    }
}
