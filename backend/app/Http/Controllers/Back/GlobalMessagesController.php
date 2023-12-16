<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\GlobalMessages;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GlobalMessagesController extends Controller
{
    public function index()
    {
        $global_messages = GlobalMessages::with('company')->get();
        return Inertia::render('Back/Settings/GlobalMessages/Index', [
            'global_messages' => $global_messages,
        ]);
    }

    public function create()
    {
        return Inertia::render('Back/Settings/GlobalMessages/Create', [
            'companies' => Company::get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'body' => 'required|string',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
        ]);

        GlobalMessages::create($request->all());

        return redirect()->route('back.settings.global-messages.index');
    }

    public function delete($id)
    {
        $global_message = GlobalMessages::findOrFail($id);
        $global_message->delete();
        return redirect()->route('back.settings.global-messages.index');
    }
}
