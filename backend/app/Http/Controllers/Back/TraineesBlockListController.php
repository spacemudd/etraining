<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\TraineeBlockList;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TraineesBlockListController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Trainees/BlockedList/Index', [
            'blocked_list' => TraineeBlockList::latest()->paginate(30),
        ]);
    }

    public function create()
    {
        return Inertia::render('Back/Trainees/BlockedList/Create');
    }

    /**
     * Store a new blocked trainee record.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'phone_additional' => 'nullable|string|max:255',
            'reason' => 'required|string|max:255',
        ]);

        TraineeBlockList::create($request->except('_token'));

        return redirect()->route('back.trainees.block-list.index');
    }

    /**
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        TraineeBlockList::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
        ]);
    }
}
