<?php

namespace Tests\Unit;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Jetstream\AddTeamMember;
use App\Models\Back\Trainee;
use App\Models\User;
use Tests\TestCase;

class CleanUpThePhoneUnitTest extends TestCase
{
    public function test_an_arabic_number_is_converted_properly()
    {
        $admin = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $trainee = Trainee::factory()->create([
            'team_id' => $admin->currentTeam()->first()->id,
            'phone' => '٠٥٠٤١٢٣٣١٨',
        ]);

        $trainee->update([
            'phone' => '٥٠٤١٢٣٣١٨',
        ]);

        $this->assertEquals('966504123318', $trainee->routeNotificationForClickSend());
    }
}
