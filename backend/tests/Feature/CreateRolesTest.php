<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Role;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateRolesTest extends TestCase
{
    use WithFaker;

    /**
     * @var \App\Models\User
     */
    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);
    }

    public function test_admin_can_see_roles_settings_in_settings_page()
    {
        $this->actingAs($this->admin)
            ->get(route('back.settings'))
            ->assertSeeText('back.settings.roles.index');
    }

    public function test_admin_can_see_roles_index()
    {
        $teamRoles = Role::where('team_id', $this->admin->currentTeam->id)->get();

        $response = $this->actingAs($this->admin)
            ->get(route('back.settings.roles.index'));

        $response->assertPropCount('roles', $teamRoles->count());

        $rolesNames = $teamRoles->pluck('name');

        $response->assertPropValue('roles', function($roles) use ($rolesNames) {
            $json_roles = json_encode($roles);
            foreach ($rolesNames as $name) {
                $this->assertStringContainsString($name, $json_roles);
            }
        });
    }

    public function test_admin_can_view_role()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();

        $this->actingAs($this->admin)
            ->get(route('back.settings.roles.show', $adminRole->id))
            ->assertSuccessful();
    }

    public function test_admin_can_find_user_roles_by_email()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();

        $response = $this->actingAs($this->admin)
            ->getJson(route('back.settings.roles.find-by-email', [
                'email' => $this->admin->email,
            ]));

        $response->assertSuccessful()
            ->assertJsonPath('user.email', $this->admin->email)
            ->assertJsonPath('user.id', $this->admin->id)
            ->assertJsonFragment([
                'id' => $adminRole->id,
                'name' => $adminRole->name,
            ]);
    }

    public function test_find_by_email_returns_not_found_for_unknown_email()
    {
        $this->actingAs($this->admin)
            ->getJson(route('back.settings.roles.find-by-email', [
                'email' => 'nobody@example.com',
            ]))
            ->assertSuccessful()
            ->assertJsonPath('user', null)
            ->assertJsonPath('roles', []);
    }

    public function test_admin_can_delete_user_who_has_verification_codes()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();

        $staff = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
            'current_team_id' => $this->admin->currentTeam->id,
        ]);
        $staff->assignRole($adminRole);

        Verification::create([
            'user_id' => $staff->id,
            'code' => '1234',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('back.settings.roles.users.delete', [
                'role_id' => $adminRole->id,
                'user_id' => $staff->id,
            ]))
            ->assertRedirect(route('back.settings.roles.index', ['role' => $adminRole->id]));

        $this->assertSoftDeleted('users', ['id' => $staff->id]);
        $this->assertSame('Staff Member', User::withTrashed()->find($staff->id)->name);
        $this->assertNull(User::find($staff->id));
        $this->assertDatabaseMissing('verifications', ['user_id' => $staff->id]);
    }

    public function test_admin_can_move_user_to_another_role()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();
        $financeRole = Role::whereName($this->admin->currentTeam->id.'_finance')->first();

        $staff = User::create([
            'name' => 'Staff Member',
            'email' => 'staff-move@example.com',
            'password' => bcrypt('password'),
            'current_team_id' => $this->admin->currentTeam->id,
        ]);
        $staff->assignRole($adminRole);

        $response = $this->actingAs($this->admin)
            ->postJson(route('back.settings.roles.users.move'), [
                'user_id' => $staff->id,
                'from_role_id' => $adminRole->id,
                'to_role_id' => $financeRole->id,
            ]);

        $response->assertSuccessful()
            ->assertJsonPath('to_role_id', $financeRole->id)
            ->assertJsonPath('from_role_id', $adminRole->id);

        $staff->refresh();
        $this->assertFalse($staff->hasRole($adminRole));
        $this->assertTrue($staff->hasRole($financeRole));
        $this->assertSame(1, $staff->roles()->count());
    }

    public function test_admin_cannot_move_themselves_to_another_role()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();
        $financeRole = Role::whereName($this->admin->currentTeam->id.'_finance')->first();

        $this->actingAs($this->admin)
            ->postJson(route('back.settings.roles.users.move'), [
                'user_id' => $this->admin->id,
                'from_role_id' => $adminRole->id,
                'to_role_id' => $financeRole->id,
            ])
            ->assertForbidden();

        $this->assertTrue($this->admin->fresh()->hasRole($adminRole));
    }

    public function test_admin_cannot_move_user_to_the_same_role()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();

        $staff = User::create([
            'name' => 'Same Role Staff',
            'email' => 'same-role@example.com',
            'password' => bcrypt('password'),
            'current_team_id' => $this->admin->currentTeam->id,
        ]);
        $staff->assignRole($adminRole);

        $this->actingAs($this->admin)
            ->postJson(route('back.settings.roles.users.move'), [
                'user_id' => $staff->id,
                'from_role_id' => $adminRole->id,
                'to_role_id' => $adminRole->id,
            ])
            ->assertStatus(422);
    }

    public function test_admin_can_assign_additional_role_to_user()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();
        $financeRole = Role::whereName($this->admin->currentTeam->id.'_finance')->first();

        $staff = User::create([
            'name' => 'Multi Role Staff',
            'email' => 'staff-assign@example.com',
            'password' => bcrypt('password'),
            'current_team_id' => $this->admin->currentTeam->id,
        ]);
        $staff->assignRole($adminRole);

        $response = $this->actingAs($this->admin)
            ->postJson(route('back.settings.roles.users.assign'), [
                'user_id' => $staff->id,
                'role_id' => $financeRole->id,
            ]);

        $response->assertSuccessful()
            ->assertJsonPath('role_id', $financeRole->id);

        $staff->refresh();
        $this->assertTrue($staff->hasRole($adminRole));
        $this->assertTrue($staff->hasRole($financeRole));
        $this->assertSame(2, $staff->roles()->count());
    }

    public function test_admin_cannot_assign_role_user_already_has()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();

        $staff = User::create([
            'name' => 'Already Has Role',
            'email' => 'staff-already@example.com',
            'password' => bcrypt('password'),
            'current_team_id' => $this->admin->currentTeam->id,
        ]);
        $staff->assignRole($adminRole);

        $this->actingAs($this->admin)
            ->postJson(route('back.settings.roles.users.assign'), [
                'user_id' => $staff->id,
                'role_id' => $adminRole->id,
            ])
            ->assertStatus(422);

        $this->assertSame(1, $staff->fresh()->roles()->count());
    }

    public function test_admin_can_remove_one_of_multiple_roles()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();
        $financeRole = Role::whereName($this->admin->currentTeam->id.'_finance')->first();

        $staff = User::create([
            'name' => 'Remove Role Staff',
            'email' => 'staff-remove@example.com',
            'password' => bcrypt('password'),
            'current_team_id' => $this->admin->currentTeam->id,
        ]);
        $staff->assignRole($adminRole);
        $staff->assignRole($financeRole);

        $response = $this->actingAs($this->admin)
            ->postJson(route('back.settings.roles.users.remove-role'), [
                'user_id' => $staff->id,
                'role_id' => $financeRole->id,
            ]);

        $response->assertSuccessful()
            ->assertJsonPath('role_id', $financeRole->id);

        $staff->refresh();
        $this->assertTrue($staff->hasRole($adminRole));
        $this->assertFalse($staff->hasRole($financeRole));
        $this->assertSame(1, $staff->roles()->count());
    }

    public function test_admin_cannot_remove_last_role_from_user()
    {
        $adminRole = Role::whereName($this->admin->currentTeam->id.'_admins')->first();

        $staff = User::create([
            'name' => 'Last Role Staff',
            'email' => 'staff-last-role@example.com',
            'password' => bcrypt('password'),
            'current_team_id' => $this->admin->currentTeam->id,
        ]);
        $staff->assignRole($adminRole);

        $this->actingAs($this->admin)
            ->postJson(route('back.settings.roles.users.remove-role'), [
                'user_id' => $staff->id,
                'role_id' => $adminRole->id,
            ])
            ->assertStatus(422);

        $this->assertTrue($staff->fresh()->hasRole($adminRole));
        $this->assertSame(1, $staff->fresh()->roles()->count());
    }
}
