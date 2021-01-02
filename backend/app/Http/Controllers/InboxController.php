<?php

namespace App\Http\Controllers;

use App\Models\InboxMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InboxController extends Controller
{
    public function index()
    {
        $messages = InboxMessage::owned()->with(['from', 'to']);

        $messages->owned()->where('to_id', auth()->user()->id)->update(['read_at' => now()]);

        if (auth()->user()->instructor) $page = 'Teaching/Inbox/Index';
        elseif (auth()->user()->trainee) $page = 'Trainee/Inbox/Index';
        else $page = 'Back/Inbox/Index';

        return Inertia::render('Trainees/Inbox/Index', [
            'messages' => $messages->latest()->get(),
        ]);
    }
}
