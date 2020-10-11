<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\CompanyContract;
use App\Models\Back\Course;
use App\Models\Back\Instructor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateCoursesManagementTest extends TestCase
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

    public function test_admin_can_see_all_courses_available()
    {
        $company = Company::factory()->create(['team_id' => $this->user->personalTeam()->id]);

        $contract = CompanyContract::factory()->create([
            'company_id' => $company,
            'team_id' => $this->user->personalTeam()->id,
            ]);

        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'company_contract_id' => $contract->id,
        ]);

        $pmpCourse = Course::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
            'name' => 'PMP Course',
            'classroom_count' => 25,
        ]);

        $this->actingAs($this->user)
            ->get(route('back.courses.index'))
            ->assertPropValue('courses', function ($courses) use ($pmpCourse) {
                $this->assertContains($pmpCourse->name, $courses['data'][0]);
            });
    }

    public function test_admin_can_save_new_course()
    {
        $company = Company::factory()->create(['team_id' => $this->user->personalTeam()->id]);

        $contract = CompanyContract::factory()->create([
            'company_id' => $company,
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $instructor = Instructor::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
            'company_contract_id' => $contract->id,
        ]);

        $pmpCourse = Course::factory()->make([
            'team_id' => $this->user->personalTeam()->id,
            'instructor_id' => $instructor->id,
            'name' => 'PMP Course',
            'classroom_count' => 25,
        ])->toArray();

        $this->actingAs($this->user)
            ->post(route('back.courses.store'), $pmpCourse)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('courses', $pmpCourse);
    }
}
