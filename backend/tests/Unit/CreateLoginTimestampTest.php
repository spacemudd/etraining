<?php

namespace Tests\Unit;

use App\Actions\Fortify\CreateNewUser;
use App\Jobs\LoginTimestampJob;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateLoginTimestampTest extends TestCase
{
    public function test_timestamp_job_is_dispatched()
    {
        Queue::fake();

        $shafiq = (new CreateNewUser())->create([
            'name' => 'Shafiq al-Shaar',
            'email' => 'hello1@getShafiq.com',
            'password' => 'hello123123',
            'password_confirmation' => 'hello123123',
        ]);

        $this->post(route('login'), [
            'email' => $shafiq->email,
            'password' => 'hello123123',
        ]);

        Queue::assertPushed(function(LoginTimestampJob $job) use ($shafiq) {
            return $job->user_id === $shafiq->id;
        });
    }
}
