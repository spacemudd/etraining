<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Trainer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TrainersManagementTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);
    }

    public function test_user_can_access_trainer_management_page()
    {
        $trainer = Trainer::factory()->create(['team_id' => $this->user->personalTeam()->id]);

        $this->actingAs($this->user)
            ->get(route('back.trainers.index'))
            ->assertSuccessful()
            ->assertPropValue('trainers', function ($trainers) use ($trainer) {
                $this->assertContains($trainer->name, $trainers['data'][0]);
            });
    }

    public function test_user_can_see_new_trainer_form()
    {
        $this->actingAs($this->user)
            ->get(route('back.trainers.create'))
            ->assertSuccessful();
    }
}
