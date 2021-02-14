<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Services\TraineesServices;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Validator;

class RegisterTraineeController extends Controller
{
    use PasswordValidationRules;

    protected $service;

    public function __construct(TraineesServices $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        // TODO: Why is this here?
        if (optional(optional(auth()->user())->trainee)->skip_uploading_id) {
            return redirect(route('dashboard'));
        }

        return view('auth.register-trainees');
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        Validator::make($request->toArray(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'identity_number' => ['required', 'unique:trainees'],
            'birthday' => ['required'],
            'phone' => ['required', 'string', 'max:255', 'unique:users'],
            'phone_additional' => 'required|string|max:255',
            'educational_level_id' => 'required|exists:educational_levels,id',
            'city_id' => 'required|exists:cities,id',
            'marital_status_id' => 'required|exists:marital_statuses,id',
            'children_count' => 'nullable|numeric',
        ])->validate();

        Validator::make($request->toArray(), [
            'identity_number' => ['required', 'unique:instructors'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:instructors'],
            'phone' => ['required', 'string', 'max:255', 'unique:instructors'],
        ])->validate();

        Validator::make($request->toArray(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:trainees'],
            'phone' => ['required', 'string', 'max:255', 'unique:trainees'],
        ])->validate();

        \DB::beginTransaction();
        $trainee = $this->service->store($request->except('_token'));

        $user = (new CreateNewTraineeUser())->create([
            'trainee_id' => $trainee->id,
            'name' => $trainee->name,
            'email' => $trainee->email,
            'phone' => $trainee->phone,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        \DB::commit();

        auth()->loginUsingId($user->id);

        return redirect()->route('dashboard');
    }

    /**
     * View the application of the logged in instructor.
     *
     * @return \Inertia\Response
     */
    public function application(): \Inertia\Response
    {
        $trainee = auth()->user()->trainee;
        return Inertia::render('Trainees/Application', [
            'is_pending_approval_prop' => $trainee->is_pending_approval,
            'trainee_id' => $trainee->id,
            'trainee_email' => $trainee->email,
        ]);
    }
}
