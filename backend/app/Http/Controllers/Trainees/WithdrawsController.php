<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\TraineeWithdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WithdrawsController extends Controller
{
    public function create()
    {
        if (auth()->user()->trainee->withdraws()->where('approved_at', null)->count()) {
            return redirect()->route('trainees.profile.index');
        }

        return Inertia::render('Trainees/Withdraws/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string',
            'files.*' => 'required|file',
        ]);

        DB::beginTransaction();
        $withdraw = auth()->user()->trainee->withdraws()->create([
            'company_id' => auth()->user()->trainee->company_id,
            'reason' => $request->reason,
        ]);
        foreach ($request->file('files', []) as $key => $file) {
            $withdraw->addMedia($file)
                ->sanitizingFileName(function ($fileName) {
                    return Str::slug(Str::beforeLast($fileName, '.')) . '.' . Str::afterLast($fileName, '.');
                })
                ->toMediaCollection();
        }
        DB::commit();

        return redirect()->route('trainees.profile.index');
    }
}
