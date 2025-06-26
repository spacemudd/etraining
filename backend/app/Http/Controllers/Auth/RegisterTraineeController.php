<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Services\TraineesServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        Log::info('RegisterTraineeController@store: Beginning trainee registration.');

        Validator::make($request->toArray(), [
            'name' => ['required', 'string', 'max:255', 'unique:trainee_block_lists'],
        ])->validate();
        Log::info('RegisterTraineeController@store: Name validation passed.');

        Validator::make($request->toArray(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ])->validate();
        Log::info('RegisterTraineeController@store: Email validation (users) passed.');

        Validator::make($request->toArray(), [
            'email' => ['unique:instructors'],
        ])->validate();
        Log::info('RegisterTraineeController@store: Email validation (instructors) passed.');

        Validator::make($request->toArray(), [
            'email' => ['unique:trainees'],
        ])->validate();
        Log::info('RegisterTraineeController@store: Email validation (trainees) passed.');

        Validator::make($request->toArray(), [
            'email' => ['unique:trainee_block_lists'],
        ])->validate();
        Log::info('RegisterTraineeController@store: Email validation passed.');

        Validator::make($request->toArray(), [
            'identity_number' => ['required', 'unique:trainees', 'unique:instructors', 'unique:trainee_block_lists'],
        ])->validate();
        Log::info('RegisterTraineeController@store: Identity number validation passed.');

        Validator::make($request->toArray(), [
            'phone' => ['required', 'string', 'max:255', 'unique:users', 'unique:instructors', 'unique:trainees', 'unique:trainee_block_lists'],
        ])->validate();
        Log::info('RegisterTraineeController@store: Phone validation passed.');

        Validator::make($request->toArray(), [
            'phone_additional' => ['required', 'string', 'max:255', 'unique:trainee_block_lists'],
        ])->validate();
        Log::info('RegisterTraineeController@store: Uniqueness validation passed.');

        Validator::make($request->toArray(), [
            'password' => $this->passwordRules(),
        ])->validate();
        Log::info('RegisterTraineeController@store: Password validation passed.');

        Validator::make($request->toArray(), [
            'birthday' => ['required'],
            'educational_level_id' => 'required|exists:educational_levels,id',
            'city_id' => 'required|exists:cities,id',
            'marital_status_id' => 'required|exists:marital_statuses,id',
            'children_count' => 'nullable|numeric',
        ])->validate();

        Log::info('RegisterTraineeController@store: All validation successful.');

        \DB::beginTransaction();
        $trainee = $this->service->store($request->except('_token'));

        Log::info('RegisterTraineeController@store: Trainee stored successfully.', ['trainee_id' => $trainee->id]);

        $user = (new CreateNewTraineeUser())->create([
            'trainee_id' => $trainee->id,
            'name' => $trainee->name,
            'email' => $trainee->email,
            'phone' => $trainee->phone,
            'national_address' => $trainee->national_address,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);
        \DB::commit();

        Log::info('RegisterTraineeController@store: User created and trainee linked.', ['user_id' => $user->id, 'trainee_id' => $trainee->id]);

        auth()->loginUsingId($user->id);

        Log::info('RegisterTraineeController@store: User logged in successfully.', ['user_id' => $user->id]);

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
