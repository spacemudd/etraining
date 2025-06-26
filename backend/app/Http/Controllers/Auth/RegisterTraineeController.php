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
            'name' => ['required', 'string', 'max:255', 'unique:trainee_block_lists'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'unique:instructors', 'unique:trainees', 'unique:trainee_block_lists'],
            'password' => $this->passwordRules(),
            'identity_number' => ['required', 'unique:trainees', 'unique:instructors', 'unique:trainee_block_lists'],
            'birthday' => ['required'],
            'phone' => ['required', 'string', 'max:255', 'unique:users', 'unique:instructors', 'unique:trainees', 'unique:trainee_block_lists'],
            'phone_additional' => ['required', 'string', 'max:255', 'unique:trainee_block_lists'],
            'educational_level_id' => 'required|exists:educational_levels,id',
            'city_id' => 'required|exists:cities,id',
            'marital_status_id' => 'required|exists:marital_statuses,id',
            'children_count' => 'nullable|numeric',
        ])->validate();

        \DB::beginTransaction();
        $trainee = $this->service->store($request->except('_token'));

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
