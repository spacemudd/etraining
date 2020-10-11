<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Trainee;
use App\Models\Back\Trainer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
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

    public function test_user_can_create_a_instructor()
    {
        $trainer = [
            'name' => 'Shafiq al-Shaar',
            'identity_number' => '10000',
            'phone' => '96670000000',
            'email' => 'hello@getshafiq.com',
            'twitter_link' => 'https://twitter.com/getshafiq',
            'city_id' => null,
        ];

        $this->actingAs($this->user)
            ->post(route('back.trainers.store'), $trainer)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('trainers', $trainer);
    }

    public function test_uploading_and_deleting_cv_full_file()
    {
        Storage::fake('s3');

        $trainer = Trainer::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('back.trainers.attachments.cv-full', ['trainer_id' => $trainer->id]), [
                'file' => UploadedFile::fake()->create('cv-full-copy.jpg', 1024 * 24),
            ])
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('media', [
            'model_id' => $trainer->id,
            'file_name' => 'cv-full-copy.jpg',
        ]);

        $this->actingAs($this->user)
            ->delete(route('back.trainers.attachments.cv-full.destroy', ['trainer_id' => $trainer->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('media', [
            'model_id' => $trainer->id,
            'file_name' => 'cv-full-copy.jpg',
        ]);
    }

    public function test_uploading_and_deleting_cv_summary_file()
    {
        Storage::fake('s3');

        $trainer = Trainer::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('back.trainers.attachments.cv-summary', ['trainer_id' => $trainer->id]), [
                'file' => UploadedFile::fake()->create('cv-summary-copy.jpg', 1024 * 24),
            ])
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('media', [
            'model_id' => $trainer->id,
            'file_name' => 'cv-summary-copy.jpg',
        ]);

        $this->actingAs($this->user)
            ->delete(route('back.trainers.attachments.cv-summary.destroy', ['trainer_id' => $trainer->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('media', [
            'model_id' => $trainer->id,
            'file_name' => 'cv-summary-copy.jpg',
        ]);
    }
}
