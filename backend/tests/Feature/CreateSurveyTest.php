<?php

namespace Tests\Feature;

use App\Actions\Jetstream\AddTeamMember;
use App\Models\Back\SurveyLink;
use App\Models\Back\Trainee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateSurveyTest extends TestCase
{
    public function test_edit_survey_links_permissions()
    {
        $shafiq = $this->makeMeAnAdmin();

        $this->actingAs($shafiq)
            ->get(route('back.settings.survey-links.index'))
            ->assertSuccessful();

        $shafiq->roles()->first()->revokePermissionTo('edit-survey-links');

        $this->actingAs($shafiq)
            ->get(route('back.settings.survey-links.index'))
            ->assertForbidden();
    }

    public function test_saving_survey_link_for_instructor()
    {
        $shafiq = $this->makeMeAnAdmin();

        $this->actingAs($shafiq)
            ->post(route('back.settings.survey-links.store'), [
                'type' => 'instructor',
                'url' => 'https://clarastars.com/instructor-survey',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('back.settings.survey-links.index'));

        $this->assertDatabaseHas('survey_links', [
            'type' => 'instructor',
            'url' => 'https://clarastars.com/instructor-survey',
        ]);
    }

    public function test_saving_survey_link_for_trainee()
    {
        $shafiq = $this->makeMeAnAdmin();

        $this->actingAs($shafiq)
            ->post(route('back.settings.survey-links.store'), [
                'type' => 'trainee',
                'url' => 'https://clarastars.com/trainee-survey',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('back.settings.survey-links.index'));

        $this->assertDatabaseHas('survey_links', [
            'type' => 'trainee',
            'url' => 'https://clarastars.com/trainee-survey',
        ]);
    }

    /**
     *
     */
    public function test_asking_trainee_to_take_survey_after_course_is_finished()
    {
        $shafiq = $this->makeMeAnAdmin();
        $team = $shafiq->currentTeam;

        $sl = new SurveyLink();
        $sl->team_id = $team->id;
        $sl->type = 'trainee';
        $sl->url = 'https://clarastars.com/shafiq-alshaar';
        $sl->save();

        // Trainee (1).
        $majdaTrainee = User::factory()->create([
            'email' => 'majda@gmail.com',
        ]);
        (new AddTeamMember())->add($majdaTrainee, $team, $majdaTrainee->email, 'trainee');
        $majdaTrainee->current_team_id = $team->id;
        $majdaTrainee->save();
        Trainee::factory()->create([
            'team_id' => $majdaTrainee->current_team_id,
            'user_id' => $majdaTrainee->id,
            'email' => $majdaTrainee->email,
        ]);

        $this->actingAs($majdaTrainee)
            ->get(route('back.session-completed-landing'))
            ->assertSessionHasNoErrors()
            ->assertSuccessful()
            ->assertPropValue('url', function ($url) use ($sl) {
                return $url === $sl->url;
            });
    }
}
