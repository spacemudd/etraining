<?php


use App\Models\User;
use App\Services\RolesService;
use Tests\TestCase;

class UpdateAttendanceEmailsUnitTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_attendance_email()
    {
        // created user
        $admin = User::factory()->create();

        $team = $admin->ownedTeams()->create([
            'name' => 'eTraining Shafiq',
            'personal_team' => false,
        ]);
        app()->make(RolesService::class)->seedRolesToTeam($team);

        $company = \App\Models\Back\Company::factory()->create([
            'team_id' => $team->id
        ]);

        $response = $this->post(route('back.reports.company-attendance.store'), [
            'company_id' => $company->id,
        ]);

        dd($response->getContent());
    }
}
