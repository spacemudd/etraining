<?php

namespace App\Http\Controllers\Back;

use App\Actions\Jetstream\AddTeamMember;
use App\Http\Controllers\Controller;
use App\Models\Back\Invite;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Notifications\InvitationToSystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-permissions');

        $roles = Role::query()
            ->withCount('users')
            ->with('permissions:id,name')
            ->get()
            ->map(static function (Role $role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'role_description' => $role->role_description,
                    'order' => $role->order,
                    'can_manage_users' => (bool) $role->can_manage_users,
                    'users_count' => (int) $role->users_count,
                    'permissions' => $role->permissions
                        ->map(static fn (Permission $permission) => [
                            'id' => $permission->id,
                            'name' => $permission->name,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values();

        $permissions = Permission::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(static fn (Permission $permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
            ])
            ->values();

        return Inertia::render('Back/Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'selectedRoleId' => $request->query('role'),
            'canEditPermissions' => auth()->user()->can('edit-permissions'),
        ]);
    }

    /**
     * Lightweight users list for a role (loaded on demand).
     */
    public function users(Request $request, string $id)
    {
        $this->authorize('view-permissions');

        $role = Role::query()->findOrFail($id);
        $perPage = min(max((int) $request->query('per_page', 100), 1), 200);

        $paginator = $role->users()
            ->select(['users.id', 'users.name', 'users.email'])
            ->orderBy('users.name')
            ->paginate($perPage);

        $users = collect($paginator->items())->map(static function (User $user) {
            $user->setAppends([]);

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        })->values();

        return response()->json([
            'users' => $users,
            'users_count' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    /**
     * Look up which role(s) a user belongs to by email.
     */
    public function findByEmail(Request $request)
    {
        $this->authorize('view-permissions');

        $validated = $request->validate([
            'email' => 'required|string|max:255',
        ]);

        $email = trim($validated['email']);

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
            ->first();

        if (! $user) {
            return response()->json([
                'user' => null,
                'roles' => [],
                'message' => __('words.user-email-not-found'),
            ]);
        }

        $user->setAppends([]);

        $roles = $user->roles()
            ->get()
            ->map(static function (Role $role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                ];
            })
            ->values();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'roles' => $roles,
        ]);
    }

    /**
     * Move a user so they belong only to the given role.
     */
    public function moveUser(Request $request)
    {
        $this->authorize('view-permissions');

        $validated = $request->validate([
            'user_id' => 'required|string|exists:users,id',
            'to_role_id' => 'required|string|exists:roles,id',
            'from_role_id' => 'nullable|string|exists:roles,id',
        ]);

        abort_if($validated['user_id'] === auth()->id(), 403);

        $user = User::query()->findOrFail($validated['user_id']);
        $toRole = Role::query()->findOrFail($validated['to_role_id']);
        $fromRole = ! empty($validated['from_role_id'])
            ? Role::query()->findOrFail($validated['from_role_id'])
            : null;

        if ($fromRole && $fromRole->id === $toRole->id) {
            return response()->json([
                'message' => __('words.user-move-same-role'),
            ], 422);
        }

        if ($fromRole && ! $user->roles()->where('roles.id', $fromRole->id)->exists()) {
            return response()->json([
                'message' => __('words.user-not-in-role'),
            ], 422);
        }

        $alreadyOnlyTarget = $user->roles()->where('roles.id', $toRole->id)->exists()
            && $user->roles()->count() === 1;

        if ($alreadyOnlyTarget) {
            return response()->json([
                'message' => __('words.user-move-same-role'),
            ], 422);
        }

        $previousRoles = $user->roles()->get();

        DB::transaction(static function () use ($user, $toRole): void {
            $user->syncRoles([$toRole]);
        });

        $user->unsetRelation('roles');

        $affectedRoles = $previousRoles
            ->push($toRole)
            ->unique('id')
            ->values();

        $roleCounts = $affectedRoles->mapWithKeys(static function (Role $role) {
            return [$role->id => (int) $role->users()->count()];
        });

        return response()->json([
            'message' => __('words.user-moved'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'from_role_id' => $fromRole ? $fromRole->id : null,
            'to_role_id' => $toRole->id,
            'from_users_count' => $fromRole ? (int) ($roleCounts[$fromRole->id] ?? $fromRole->users()->count()) : null,
            'to_users_count' => (int) ($roleCounts[$toRole->id] ?? $toRole->users()->count()),
            'role_counts' => $roleCounts->all(),
            'roles' => $user->roles()
                ->get()
                ->map(static function (Role $role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                    ];
                })
                ->values(),
        ]);
    }

    /**
     * Show role.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        Role::query()->findOrFail($id);

        return redirect()->route('back.settings.roles.index', ['role' => $id]);
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

        return redirect()->route('back.settings.roles.index', ['role' => $id]);
    }

    /**
     *
     * @param $role_id
     * @param $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($role_id, $user_id)
    {
        $this->authorize('view-permissions');

        abort_if($user_id === auth()->id(), 403);

        $user = User::findOrFail($user_id);

        DB::transaction(static function () use ($user): void {
            $user->delete();
        });

        return redirect()->route('back.settings.roles.index', ['role' => $role_id]);
    }
}
