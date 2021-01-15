<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreatePermissionsTest extends TestCase
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

    public function test_admin_can_see_permissions_settings_in_settings_page()
    {
        $this->actingAs($this->admin)
            ->get(route('back.settings'))
            ->assertSeeText('back.settings.roles.index');
    }
}
