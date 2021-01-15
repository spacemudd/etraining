<?php

namespace App\Http\Controllers\Back;

use App\Actions\Jetstream\AddTeamMember;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RolesController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Roles/Index', [
            'roles' => Role::withCount('users')->with('permissions')->get()->toArray(),
        ]);
    }

    /**
     * Show role.
     *
     * @param $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        return Inertia::render('Back/Roles/Show', [
            'role' => Role::with('users')->findOrFail($id),
        ]);
    }

    /**
     * Show invite form.
     *
     * @param $id
     * @return \Inertia\Response
     */
    public function invite($id)
    {
        return Inertia::render('Back/Roles/Invites/Create', [
            'role' => Role::findOrFail($id),
        ]);
    }

    /**
     *
     * @param $id
     * @param \Illuminate\Http\Request $request
     */
    public function sendInvite($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|unique:users,email',
            'password' => 'required|string',
        ]);

        $role = Role::findOrFail($id);
        $team = auth()->user()->currentTeam;

        DB::beginTransaction();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
        ]);

        $user->assignRole($role);
        $user->current_team_id = $team->id;
        $user->save();

        (new AddTeamMember())->add($user, $team, $user->email, 'admin');
        DB::commit();

        return redirect()->route('back.settings.roles.show', $role->id);
    }

    /**
     *
     * @param $role_id
     * @param $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($role_id, $user_id)
    {
        User::findOrFail($user_id)->delete();
        return redirect()->route('back.settings.roles.show', $role_id);
    }
}
