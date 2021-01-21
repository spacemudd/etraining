<?php

namespace App\Http\Controllers;

use App\Actions\Jetstream\AddTeamMember;
use App\Models\Back\Invite;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InviteController extends Controller
{
    /**
     *
     * @param $invite_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($invite_id)
    {
        $invite = Invite::findOrFail($invite_id);
        return view('invite.show', compact('invite'));
    }

    public function accept($invite_id, Request $request)
    {
        $request->validate([
            'password' => 'required|min:8',
        ]);

        DB::beginTransaction();
        $invite = Invite::findOrFail($invite_id);
        $role = Role::findOrFail($invite->role_id);
        $team = Team::findOrFail($invite->team_id);
        $user = User::create([
            'name' => $invite->name,
            'email' => $invite->email,
            'phone' => $invite->phone,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole($role);
        $user->current_team_id = $team->id;
        $user->save();
        (new AddTeamMember())->add($user, $team, $user->email, 'admin');
        DB::commit();

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
