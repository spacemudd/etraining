<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class IdentityViewerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::info('IdentityViewerUserSeeder: Starting to create identity viewer user');

        // Get the first team (or create one if needed)
        $team = Team::first();
        
        if (!$team) {
            Log::error('IdentityViewerUserSeeder: No team found. Please create a team first.');
            return;
        }

        // Seed permissions first
        $permissions = [
            'search-trainees-by-identity',
            'view-trainee-identity-only'
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
            ]);
        }

        Log::info('IdentityViewerUserSeeder: Permissions created');

        // Create the role
        $roleName = $team->id . '_identity-viewer';
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'team_id' => $team->id,
        ]);

        // Assign permissions to the role
        $role->givePermissionTo($permissions);

        Log::info('IdentityViewerUserSeeder: Role created and permissions assigned', [
            'role_name' => $roleName,
            'permissions' => $permissions
        ]);

        // Check if user already exists
        $user = User::where('email', 'identity-viewer@jasarah-ksa.com')->first();

        if ($user) {
            Log::info('IdentityViewerUserSeeder: User already exists, updating role', ['user_id' => $user->id]);
            $user->assignRole($role);
            $user->current_team_id = $team->id;
            $user->save();
        } else {
            // Create the user
            $user = User::create([
                'name' => 'Identity Viewer',
                'email' => 'identity-viewer@jasarah-ksa.com',
                'password' => Hash::make('password123'), // Default password, should be changed
                'current_team_id' => $team->id,
            ]);

            // Assign role to user
            $user->assignRole($role);

            Log::info('IdentityViewerUserSeeder: User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $roleName
            ]);
        }

        Log::info('IdentityViewerUserSeeder: Completed successfully');
    }
}

