<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Services\TraineesServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Scope\TeamScope;
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

        User::withoutGlobalScope(TeamScope::class);

        // Clean and prepare data
        $data = $request->except('_token');
        $data['children_count'] = $data['children_count'] ? (int) $data['children_count'] : 0;

        Validator::make($data, [
            'name' => ['required', 'string', 'max:255', 'unique:trainee_block_lists'],
            'english_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'unique:instructors', 'unique:trainees', 'unique:trainee_block_lists'],
            'password' => $this->passwordRules(),
            'identity_number' => ['required', 'string', 'max:255', 'unique:trainees', 'unique:instructors', 'unique:trainee_block_lists'],
            'birthday' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:255', 'unique:users', 'unique:instructors', 'unique:trainees', 'unique:trainee_block_lists'],
            'phone_additional' => ['required', 'string', 'max:255', 'unique:trainee_block_lists'],
            'educational_level_id' => 'required|exists:educational_levels,id',
            'city_id' => 'required|exists:cities,id',
            'marital_status_id' => 'required|exists:marital_statuses,id',
            'children_count' => 'nullable|integer|min:0|max:20',
        ])->validate();

        User::addGlobalScope(new TeamScope());

        Log::info('RegisterTraineeController@store: Validation successful.');

        \DB::beginTransaction();
        try {
            $trainee = $this->service->store($data);

            $user = (new CreateNewTraineeUser())->create([
                'trainee_id' => $trainee->id,
                'name' => $trainee->name,
                'email' => $trainee->email,
                'phone' => $trainee->phone,
                'national_address' => $trainee->national_address ?? '',
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);
            \DB::commit();

            Log::info('RegisterTraineeController@store: Trainee registration successful.', [
                'trainee_id' => $trainee->id,
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // Redirect to login page with a success message
            return redirect()->route('login')->with('status', 'تم التسجيل بنجاح! الرجاء تسجيل الدخول.');
        } catch (\Throwable $e) {
            \DB::rollBack();
            Log::error('[REGISTRATION] Exception', [
                'step' => 'exception',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return back with error message
            return back()->withErrors(['error' => 'حدث خطأ أثناء التسجيل. الرجاء المحاولة مرة أخرى.'])->withInput();
        }
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
