<?php

namespace App\Http\Controllers;

use App\Models\Back\Instructor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ZoomAccountController extends Controller
{
    public function index($instructor_id)
    {
        $instructor = Instructor::with('zoom_account')->find($instructor_id);

        return Inertia::render('Back/Instructors/ZoomAccount/Index', [
            'instructor' => $instructor,
        ]);
    }

    public function edit($instruct_id)
    {
        return Inertia::render('Instructors/ZoomAccount/Edit', [
            'instructor' => Instructor::with('zoom_account')->find($instruct_id),
        ]);
    }

    public function update(Request $request, $instructor_id)
    {
        $request->validate([
            'account_id' => 'required',
            'client_id' => 'required',
            'client_secret' => 'required',
        ]);

        $instructor = Instructor::find($instructor_id);

        if ($instructor->zoom_account) {
            $instructor->zoom_account->update([
                'account_id' => $request->account_id,
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
            ]);
        } else {
            $instructor->zoom_account()->create([
                'account_id' => $request->account_id,
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
            ]);
        }

        return redirect()->route('back.instructors.show', $instructor_id);
    }
}
