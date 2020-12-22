<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewInstructorUser;
use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Back\Instructor;
use App\Services\InstructorServices;
use Illuminate\Http\Request;
use Validator;
use Inertia\Inertia;

class RegisterInstructorController extends Controller
{
    use PasswordValidationRules;

    protected $service;

    public function __construct(InstructorServices $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        return view('auth.register-instructors');
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     */
    public function FormStore(Request $request)
    {

        Validator::make($request->toArray(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'identity_number' => ['required'],
            'phone' => ['required', 'string', 'max:255'],
            'provided_courses' => ['required', 'string', 'max:255'],
            'twitter_link' => 'nullable|url',
            'city_id' => 'required|exists:cities,id',

        ])->validate();



        \DB::beginTransaction();
        $instructor = (new CreateNewInstructorUser())->storeRegisterationForm([
            'name' => $request['name'],
            'email' => $request['email'],
            'identity_number' => $request['identity_number'],
            'phone' => $request['phone'],
            'provided_courses' => $request['provided_courses'],
            'city_id' => $request['city_id'],
            'twitter_link' => $request['twitter_link'],
        ]);

        \DB::commit();

        $instructor = Instructor::where('email', $request['email'])->first();
        return Inertia::render('Instructors/Application', [
            'instructor_id' => $instructor->id,
            'instructor_email' => $instructor->email,
        ]);
    }


    // Needs Modification Based On The Values That Will Be Filled By The System Admin From The Dashboard
    public function createUser(Request $request)
    {
        Validator::make($request->toArray(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'identity_number' => ['required'],
            'phone' => ['required', 'string', 'max:255'],
            'provided_courses' => ['required', 'string', 'max:255'],
            'twitter_link' => ['required', 'string', 'max:255'],

        ])->validate();

        \DB::beginTransaction();
        $instructor = $this->service->store($request->except('_token'));

        $instructor = (new CreateNewInstructorUser())->storeRegisterationForm([
            'trainee_id' => $trainee->id,
            'name' => $trainee->name,
            'email' => $trainee->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        \DB::commit();

        return redirect()->route('dashboard');
    }
}
