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

        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
        $this->assertDatabaseMissing('verifications', ['user_id' => $staff->id]);
    }
}
