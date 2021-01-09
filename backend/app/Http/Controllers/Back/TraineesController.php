<?php

namespace App\Http\Controllers\Back;

use App\Actions\Fortify\CreateNewInstructorUser;
use App\Actions\Fortify\CreateNewTraineeUser;
use App\Http\Controllers\Controller;
use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
use App\Models\Media;
use App\Models\User;
use App\Notifications\InstructorApplicationApprovedNotification;
use App\Notifications\InstructorWelcomeNotification;
use App\Notifications\TraineeWelcomeNotification;
use App\Services\RolesService;
use App\Services\TraineesServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TraineesController extends Controller
{
    protected $service;

    public function __construct(TraineesServices $services)
    {
        $this->service = $services;
    }

    public function index()
    {
        return Inertia::render('Back/Trainees/Index', [
            'trainees' => Trainee::with('company')->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Back/Trainees/Create', [
            'trainee_groups' => TraineeGroup::get(),
            'cities' => City::orderBy('name_ar')->get(),
            'marital_statuses' => MaritalStatus::orderBy('order')->get(),
            'educational_levels' => EducationalLevel::orderBy('order')->get(),
        ]);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'trainee_group_name' => 'nullable|string|max:255',
            'email' => 'string|max:255',
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:255',
            'birthday' => 'required|date',
            'educational_level_id' => 'nullable|exists:educational_levels,id',
            'city_id' => 'nullable|exists:cities,id',
            'marital_status_id' => 'nullable|exists:marital_statuses,id',
            'children_count' => 'nullable|integer|max:20',
        ]);

        $trainee = $this->service->store($request->except('_token'));

        return redirect()->route('back.trainees.show', $trainee->id);
    }

    /**
     *
     * @param $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        return Inertia::render('Back/Trainees/Show', [
            'trainee' => Trainee::with(['educational_level', 'city', 'marital_status', 'trainee_group'])->findOrFail($id),
            'trainee_groups' => TraineeGroup::get(),
            'cities' => City::orderBy('name_ar')->get(),
            'marital_statuses' => MaritalStatus::orderBy('order')->get(),
            'educational_levels' => EducationalLevel::orderBy('order')->get(),
        ]);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return
     */
    public function storeIdentity(Request $request, $trainee_id)
    {
        $request->validate([
            'identity_card_copy' => 'required_without:file',
            'file' => 'required_without:identity_card_copy',
        ]);

        $trainee = Trainee::findOrFail($trainee_id);
        $file = $request->file('identity_card_copy') ?: $request->file('file');
        $uploaded_file = $trainee->uploadToFolder($file, 'identity');

        // When the other has been filled, mark the application as pending approval from the administration.
        if ($trainee->identity_card_url) {
            $trainee->status = Trainee::STATUS_PENDING_APPROVAL;
            $trainee->save();
        }

        return $uploaded_file;
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteIdentity(Request $request, $trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);
        $trainee->getMedia('identity')->each->forceDelete();

        $trainee->status = Instructor::STATUS_PENDING_UPLOADING_FILES;
        $trainee->save();

        return response()->redirectToRoute('back.trainees.show', $trainee->id);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return
     */
    public function storeQualification(Request $request, $trainee_id)
    {
        $request->validate([
            'qualification_copy' => 'required_without:file',
            'file' => 'required_without:qualification_copy',
        ]);

        $trainee = Trainee::findOrFail($trainee_id);
        $file = $request->file('qualification_copy') ?: $request->file('file');
        $uploaded_file = $trainee->uploadToFolder($file, 'qualification');

        // When the other has been filled, mark the application as pending approval from the administration.
        if ($trainee->qualification_copy_url) {
            $trainee->status = Trainee::STATUS_PENDING_APPROVAL;
            $trainee->save();
        }

        return $uploaded_file;
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteQualification(Request $request, $trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);
        $trainee->getMedia('qualification')->each->forceDelete();

        $trainee->status = Instructor::STATUS_PENDING_UPLOADING_FILES;
        $trainee->save();

        return response()->redirectToRoute('back.trainees.show', $trainee->id);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return
     */
    public function storeBankAccount(Request $request, $trainee_id)
    {
        $request->validate([
            'bank_account_copy' => 'required_without:file',
            'file' => 'required_without:bank_account_copy',
        ]);

        $trainee = Trainee::findOrFail($trainee_id);
        $file = $request->file('bank_account_copy') ?: $request->file('file');
        $uploaded_file = $trainee->uploadToFolder($file, 'bank-account');

        // When the other has been filled, mark the application as pending approval from the administration.
        if ($trainee->bank_account_copy_url) {
            $trainee->status = Trainee::STATUS_PENDING_APPROVAL;
            $trainee->save();
        }

        return $uploaded_file;
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteBankAccount(Request $request, $trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);
        $trainee->getMedia('bank-account')->each->forceDelete();

        $trainee->status = Instructor::STATUS_PENDING_UPLOADING_FILES;
        $trainee->save();

        return response()->redirectToRoute('back.trainees.show', $trainee->id);
    }

    /**
     * Shows all trainees with groups.
     *
     * @returns array
     */
    public function withGroups(): array
    {
        $groups = TraineeGroup::with('trainees')
            ->get()
            ->toArray();

        foreach ($groups as &$group)  {
            $group['id'] .= '-group';
        }

        return $groups;
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function assignInstructor(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'trainees.*' => 'nullable',
        ]);

        \DB::beginTransaction();
        $trainee_ids = [];

        if (count($request->trainees)) {
            foreach ($request->trainees as $trainee_id) {
                if (Str::contains($trainee_id, '-group')) {
                    $group = TraineeGroup::findOrFail(Str::before($trainee_id, '-group'));
                    $group_ids = $group->trainees()->pluck('trainees.id');
                    foreach ($group_ids as $id) {
                        $trainee_ids[] = $id;
                    }
                } else {
                    $trainee_ids[] = $trainee_id;
                }
            }
        }

        Trainee::where('instructor_id', $request->instructor_id)->update(['instructor_id' => null]);
        Trainee::whereIn('id', $trainee_ids)->update(['instructor_id' => $request->instructor_id]);

        \DB::commit();

        return redirect()->back();
    }

    /**
     * Open a new account for the trainee where they can login with it.
     *
     * @param $trainee_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createUser($trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);

        $user = (new CreateNewTraineeUser())->create([
            'trainee_id' => $trainee->id,
            'name' => $trainee->name,
            'email' => $trainee->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        return redirect()->route('back.trainees.show', $trainee->id);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function storeCvFromApplication(Request $request)
    {
        $request->validate([
            'identity_card_copy' => 'required',
            'qualification_copy' => 'required',
            'bank_account_copy' => 'required',
        ]);

        $this->storeIdentity($request, auth()->user()->trainee->id);
        $this->storeQualification($request, auth()->user()->trainee->id);
        $this->storeBankAccount($request, auth()->user()->trainee->id);

        $trainee = auth()->user()->trainee;
        $trainee->status = Trainee::STATUS_PENDING_APPROVAL;
        $trainee->save();

        Notification::send(auth()->user(), new TraineeWelcomeNotification());

        return $trainee->media;
    }

    /**
     * Approving the instructor to use the platform and start broadcasting.
     *
     * @param $trainee_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveUser($trainee_id)
    {
        //$this->authorize('approve-trainee-applicants');

        $trainee = Trainee::findOrFail($trainee_id);
        $trainee->status = Trainee::STATUS_APPROVED;
        $trainee->approved_by_id = auth()->user()->id;
        $trainee->approved_at = now();
        $trainee->save();

        Notification::send($trainee->user, new InstructorApplicationApprovedNotification());

        Log::info('Trainee ID: '.$trainee->id.' has been approved by user: '.auth()->user()->email);

        return redirect()->route('back.trainees.show', $trainee->id);
    }

    public function update(Request $request, $trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);

        $request->validate([
            'trainee_group_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'identity_number' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'educational_level_id' => 'nullable|exists:educational_levels,id',
            'city_id' => 'nullable|exists:cities,id',
            'marital_status_id' => 'nullable|exists:marital_statuses,id',
        ]);

         $trainee->update(
            [
                "name" => $request->trainee['name'],
                "phone" => $request->trainee['phone'],
                "phone_additional" => $request->trainee['phone_additional'],
                "birthday" => $request->trainee['birthday'],
                "educational_level_id" => $request->trainee['educational_level_id'],
                "city_id" => $request->trainee['city_id'],
                "marital_status_id" => $request->trainee['marital_status_id'],
                "identity_number" => $request->trainee['identity_number'],
                "email" => $request->trainee['email'],
                "city_id" => $request->trainee['city_id'],
                "children_count" => (int)$request->trainee['children_count'],
            ]
        );

        // if (isset($traineeRequest['trainee_group_name'])) {
        //     $group = TraineeGroup::firstOrCreate([
        //         'name' => $traineeRequest['trainee_group_name'],
        //     ]);
        //     $group->trainees()->attach([$trainee->id]);
        // }

        return redirect()->route('back.trainees.show', $trainee_id);
    }

    public function blockView($trainee_id) {

        $trainee = Trainee::findOrFail($trainee_id);
        return Inertia::render('Back/Trainees/Block', [
            'trainee' => $trainee,
        ]);

    }

    public function block(Request $request, $trainee_id)
    {

        $trainee = Trainee::findOrFail($trainee_id);
        $trainee->update(
            [
                "deleted_remark" => $request->deleted_remark,
            ]);
        $trainee->delete();
        return redirect()->route('back.trainees.index');
    }

}
