<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Role;
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
}
