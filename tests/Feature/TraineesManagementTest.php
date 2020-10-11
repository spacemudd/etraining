<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Back\Trainee;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

class TraineesManagementTest extends TestCase
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

    public function test_user_can_access_trainee_management_page()
    {
        $this->actingAs($this->user)
            ->get(route('back.trainees.index'))
            ->assertSuccessful();
    }

    public function test_user_can_see_new_trainee_form()
    {
        $this->actingAs($this->user)
            ->get(route('back.trainees.create'))
            ->assertSuccessful();
    }

    public function test_user_can_create_a_trainee()
    {
        $trainee = [
            'name' => 'Shafiq al-Shaar',
            'identity_number' => '10000',
            'phone' => '96670000000',
            'phone_additional' => '97300000000',
            'birthday' => '1994-12-10',
            'educational_level_id' => null,
            'city_id' => null,
            'marital_status_id' => null,
            'children_count' => 0,
        ];

        $this->actingAs($this->user)
            ->post(route('back.trainees.store'), $trainee)
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('trainees', $trainee);
    }

    public function test_user_can_view_trainee()
    {
        $trainee = Trainee::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('back.trainees.show', $trainee->id))
            ->assertPropValue('trainee', function ($savedTrainee) use ($trainee) {
                $this->assertEquals($savedTrainee['name'], $trainee['name']);
            });
    }

    public function test_uploading_and_deleting_identity_file()
    {
        Storage::fake('s3');

        $trainee = Trainee::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('back.trainees.attachments.identity', ['trainee_id' => $trainee->id]), [
                'file' => UploadedFile::fake()->create('identity-copy.jpg', 1024 * 24),
            ])
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('media', [
            'model_id' => $trainee->id,
            'file_name' => 'identity-copy.jpg',
        ]);

        $this->actingAs($this->user)
            ->delete(route('back.trainees.attachments.identity.destroy', ['trainee_id' => $trainee->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('media', [
            'model_id' => $trainee->id,
            'file_name' => 'identity-copy.jpg',
        ]);
    }

    public function test_uploading_and_deleting_qualification_file()
    {
        Storage::fake('s3');

        $trainee = Trainee::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('back.trainees.attachments.qualification', ['trainee_id' => $trainee->id]), [
                'file' => UploadedFile::fake()->create('qualification-copy.jpg', 1024 * 24)
            ])
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('media', [
            'model_id' => $trainee->id,
            'file_name' => 'qualification-copy.jpg',
        ]);

        $this->actingAs($this->user)
            ->delete(route('back.trainees.attachments.qualification.destroy', ['trainee_id' => $trainee->id]));

        $this->assertDatabaseMissing('media', [
            'model_id' => $trainee->id,
            'file_name' => 'qualification-copy.jpg',
        ]);
    }

    public function test_uploading_and_deleting_bank_account_file()
    {
        Storage::fake('s3');

        $trainee = Trainee::factory()->create([
            'team_id' => $this->user->personalTeam()->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('back.trainees.attachments.bank-account', ['trainee_id' => $trainee->id]), [
                'file' => UploadedFile::fake()->create('bank-account-copy.jpg', 1024 * 24)
            ])
            ->assertSessionHasNoErrors()
            ->assertSuccessful();

        $this->assertDatabaseHas('media', [
            'model_id' => $trainee->id,
            'file_name' => 'bank-account-copy.jpg',
        ]);

        $this->actingAs($this->user)
            ->delete(route('back.trainees.attachments.bank-account.destroy', ['trainee_id' => $trainee->id]));

        $this->assertDatabaseMissing('media', [
            'model_id' => $trainee->id,
            'file_name' => 'bank-account-copy.jpg',
        ]);
    }
}
