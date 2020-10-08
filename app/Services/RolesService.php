<?php
/**
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Team;
use Log;

class RolesService
{
    public $permissions = [
        'create-companies',
        'edit-companies',
        'delete-companies',
        // add the same name of the role to translation files.
    ];

    public function seedPermissions()
    {
        Log::info('Beginning to seed permissions');
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }
        Log::info('Completed seeding permissions');
    }

    /**
     *
     * @param \App\Models\Team $team
     */
    public function seedRolesToTeam(Team $team)
    {
        $this->seedPermissions();

        // Role.
        $adminRole = Role::firstOrCreate(['name' => $team->id.'_admins', 'team_id' => $team->id]);
        $adminRole->givePermissionTo($this->permissions);

        Log::info('Created "Admin" role for team ID: '.$team->id);

        // Newly created user.
        if ($owner = $team->owner) {
            Log::info('Assigning admin role to the user ID of: '.$owner->id.' and under team ID: '.$team->id);
            $owner->assignRole($adminRole);
        }

        // Add default roles.
        Role::firstOrCreate(['name' => $team->id.'_trainees', 'team_id' => $team->id])->id;
        Role::firstOrCreate(['name' => $team->id.'_trainers', 'team_id' => $team->id])->id;
        Role::firstOrCreate(['name' => $team->id.'_finance', 'team_id' => $team->id])->id;
    }
}
