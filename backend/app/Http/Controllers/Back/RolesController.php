<?php

namespace App\Http\Controllers\Back;

use App\Actions\Jetstream\AddTeamMember;
use App\Http\Controllers\Controller;
use App\Models\Back\Invite;
use App\Models\Role;
use App\Models\User;
use App\Notifications\InvitationToSystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendInvite($id, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|unique:users,email',
        ]);

        $invite = Invite::make($request->except('_token'));
        $invite->role_id = $id;
        $invite->save();

        Notification::send($invite, new InvitationToSystemNotification());

        return redirect()->route('back.settings.roles.show', $id);
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
