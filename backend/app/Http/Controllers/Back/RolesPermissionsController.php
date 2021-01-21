<?php
/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RolesPermissionsController extends Controller
{
    public function index($role_id)
    {
        return Inertia::render('Back/Roles/Permissions/Index', [
            'role' => Role::with('permissions')->findOrFail($role_id),
            'permissions' => Permission::get(),
        ]);
    }

    public function attachPermission(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_name' => 'required|exists:permissions,name',
        ]);

        $role = DB::transaction(function() use ($request) {
            $role = Role::where('id', $request->get('role_id'))->lockForUpdate()->firstOrFail();
            $permission = Permission::where('name', $request->get('permission_name'))->lockForUpdate()->firstOrFail();

            return $role->givePermissionTo($permission);
        }, 5);

        return $role;
    }

    public function detachPermission(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_name' => 'required|exists:permissions,name',
        ]);

        $role = DB::transaction(function() use ($request) {
            $role = Role::where('id', $request->get('role_id'))->lockForUpdate()->firstOrFail();
            $permission = Permission::where('name', $request->get('permission_name'))->lockForUpdate()->firstOrFail();

            return $role->revokePermissionTo($permission);
        }, 5);

        return $role;
    }
}
