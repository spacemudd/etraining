<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Course;
use App\Models\Back\Instructor;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateCourseBatchesManagementTest extends TestCase
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
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_can_see_course_batch_create_page()
    {
        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $pmpCourse = Course::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
            'name_en' => 'PMP Course',
            'classroom_count' => 25,
        ]);

        $this->actingAs($this->user)
            ->get(route('back.course-batches.create', [
                'course_id' => $pmpCourse->id,
            ]))
            ->assertSuccessful();
    }
}
