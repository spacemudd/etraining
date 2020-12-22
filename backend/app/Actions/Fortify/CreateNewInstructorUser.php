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
use App\Models\Back\Instructor;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewInstructorUser implements CreatesNewUsers
{
    use PasswordValidationRules;



    public function storeRegisterationForm(array $input) {
    // First Function is intended to save the registeration Form While the second is for the admin to create an actual user.
    // I didn't want to commit the instructor a role in the roles table. In case he was refused, we will cascade quickly from this table.

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'identity_number' => ['required'],
            'phone' => ['required', 'string', 'max:255'],
            'provided_courses' => ['required', 'string', 'max:255'],
            'twitter_link' => 'nullable|url', //requires further adjustments.
            'city_id' => 'required|exists:cities,id',
        ])->validate();

        \DB::beginTransaction();
        $team_id = Team::first()->id;

        $instructor = Instructor::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'identity_number' => $input['identity_number'],
            'phone' => $input['phone'],
            'provided_courses' => $input['provided_courses'],
            'twitter_link' => $input['twitter_link'],
            'city_id' => $input['city_id'],
            'team_id' => $team_id,
        ])->save();

        \DB::commit();

        return $instructor;
    }


    // Needs Modification Based On The Values That Will Be Filled By The System Admin From The Dashboard
    public function create(array $input)
    {
        Validator::make($input, [
            'instructor_id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ])->validate();

        \DB::beginTransaction();
        $team = Team::first();
        $role = Role::findByName($team->id.'_instructors', 'web');

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->assignRole($role);

        $user->current_team_id = $team->id;
        $user->save();

        (new AddTeamMember())->add($user, $team, $input['email'], 'instructor');

        $instructor = Instructor::where('id', $input['instructor_id'])->first();
        $instructor->user_id = $user->id;
        $instructor->save();

        \DB::commit();

        return $user;
    }
}
