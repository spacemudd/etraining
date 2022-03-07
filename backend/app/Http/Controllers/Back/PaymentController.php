<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\ComplaintsSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $this->authorize('edit-complaint-settings');

        return Inertia::render('Back/Settings/PaymentSettings', [
            'online_payment' => auth()->user()->currentTeam->online_payment,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'online_payment' => 'required|boolean',
        ]);

        $team = auth()->user()->currentTeam;
        $team->online_payment = $request->online_payment;
        $team->save();

        return redirect()->route('back.settings');
    }
}
