<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\Trainee;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateCompanyAttendanceReportTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = (new CreateNewUser())->create([
            'name' => 'May',
            'email' => 'admin-test@itmaal.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
    }

    public function test_user_can_mark_trainee_as_new_registration()
    {
        $q= $this->post(route('login'), [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        dd($q->isSuccessful());

        $company = Company::factory()->create(['team_id' => $this->user->personalTeam()->id]);
        $may = Trainee::factory()->create(['team_id' => $this->user->personalTeam()->id,'company_id' => $company->id]);
        $report = CompanyAttendanceReport::factory()->create([
            'company_id' => $company->id,
            'number' => 123,
        ]);

        $r = $this->actingAs($this->user)
            ->post(route('back.reports.company-attendance.trainees.update', [
                'report_id' => $report->id,
                'trainee_id' => $may->id,
            ]), [
                'action' => 'new_registration',
            ]);
        dd($r->getContent());
            //->assertSuccessful()
            //->assertSessionHasNoErrors();

        $this->assertDatabaseHas('company_attendance_reports_trainees', [
            'trainee_id' => $may->id,
            'report_id' => $report->id,
            'action' => 'new_registration',
        ]);
    }
}
