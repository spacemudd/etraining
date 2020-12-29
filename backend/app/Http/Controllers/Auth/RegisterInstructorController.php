<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewInstructorUser;
use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Back\Instructor;
use App\Services\InstructorServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     * Make a new instructor application.
     * Redirect the new instructor to fill their application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'identity_number' => ['required'],
            'phone' => ['required', 'string', 'max:255'],
            'provided_courses' => ['required', 'string', 'max:255'],
            'twitter_link' => 'nullable|url|unique:instructors',
            'city_id' => 'required|exists:cities,id',
        ]);

        \DB::beginTransaction();
        $instructor = (new CreateNewInstructorUser())->storeApplication([
            'name' => $request['name'],
            'email' => $request['email'],
            'identity_number' => $request['identity_number'],
            'phone' => $request['phone'],
            'provided_courses' => $request['provided_courses'],
            'city_id' => $request['city_id'],
            'twitter_link' => $request['twitter_link'],
        ]);
        $instructorUser = (new CreateNewInstructorUser())->createUserFromApplication($instructor, $request['password']);
        \DB::commit();

        Log::info([
            'msg' => 'New instructor sign up',
            'email' => $instructor->email,
            'team_id' => $instructor->team_id,
        ]);

        auth()->login($instructorUser);

        return redirect()->route('register.instructors.application');
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

        $instructorUser = (new CreateNewInstructorUser())->storeApplication([
            'trainee_id' => $instructor->id,
            'name' => $instructor->name,
            'email' => $instructor->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        \DB::commit();

        return redirect()->route('dashboard');
    }

    /**
     * View the application of the logged in instructor.
     *
     * @return \Inertia\Response
     */
    public function application(): \Inertia\Response
    {
        $instructor = auth()->user()->instructor;
        return Inertia::render('Instructors/Application', [
            'is_pending_approval_prop' => $instructor->is_pending_approval,
            'instructor_id' => $instructor->id,
            'instructor_email' => $instructor->email,
        ]);
    }
}
