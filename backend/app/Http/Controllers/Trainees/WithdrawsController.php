<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Mail\TraineeWithdrawalMail;
use App\Models\Back\TraineeWithdraw;
use App\Models\User;
use App\Notifications\TraineeWithdrawalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Mail;

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

        //$users = User::permission('receive-trainee-withdrawal-notifications')->get();
        //foreach ($users as $user) {
        //    $user->notify(new TraineeWithdrawalNotification($withdraw));
        //}
        Mail::to('trainee.affairs@ptc-ksa.net')

            ->send(new TraineeWithdrawalMail($withdraw));

        dd(123);
        return redirect()->route('trainees.profile.index');
    }
}
