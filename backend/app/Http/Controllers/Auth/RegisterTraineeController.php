<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Services\TraineesServices;
use Illuminate\Http\Request;
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
        return view('auth.register-trainees');
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        Validator::make($request->toArray(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'identity_number' => ['required'],
            'birthday' => ['required'],
            'phone' => ['required', 'string', 'max:255'],
            'phone_additional' => 'required|string|max:255',
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
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        \DB::commit();

        auth()->loginUsingId($user->id);

        return redirect()->route('dashboard');
    }
}
