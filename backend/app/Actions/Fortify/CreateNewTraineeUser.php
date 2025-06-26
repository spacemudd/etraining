<?php
/**
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Actions\Fortify;

use App\Actions\Jetstream\AddTeamMember;
use App\Models\Back\Trainee;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewTraineeUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input)
    {
        Log::info('CreateNewTraineeUser@create: Beginning user creation for trainee.');
        \DB::beginTransaction();
        $team = Team::first(); // TODO.
        $role = Role::findByName($team->id.'_trainees', 'web');

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone' => $input['phone'],
        ]);

        Log::info('CreateNewTraineeUser@create: User model created.', ['user_id' => $user->id]);

        $user->assignRole($role);

        $user->current_team_id = $team->id;
        $user->save();

        Log::info('CreateNewTraineeUser@create: Role assigned and team set.', ['user_id' => $user->id, 'role' => $role->name]);

        (new AddTeamMember())->add($user, $team, $input['email'], 'trainee');

        $trainee = Trainee::where('id', $input['trainee_id'])->first();
        $trainee->user_id = $user->id;
        $trainee->save();

        Log::info('CreateNewTraineeUser@create: Trainee linked to user.', ['user_id' => $user->id, 'trainee_id' => $trainee->id]);

        \DB::commit();

        Log::info('CreateNewTraineeUser@create: User creation complete.');

        return $user;
    }
}
