<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Course;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\Instructor;
use App\Models\Back\ZoomAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateZoomTest extends TestCase
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

    public function test_signature_method_for_zoom_api()
    {
        $instructor = Instructor::factory()->create();

        ZoomAccount::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
        ]);

        $course = Course::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
        ]);

        $batch = CourseBatch::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'course_id' => $course->id,
        ]);

        CourseBatchSession::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'course_id' =>  $course->id,
            'course_batch_id' => $batch->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('api.zoom.signature'))
            ->assertSuccessful();
    }
}
