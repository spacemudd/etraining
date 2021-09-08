<?php

namespace App\Http\Controllers\Back;

use App\Actions\Fortify\CreateNewTraineeUser;
use App\Http\Controllers\Controller;
use App\Jobs\ExportTraineesToExcelJob;
use App\Jobs\ExportArchivedTraineesToExcelJob;
use App\Models\Back\AttendanceReportRecordWarning;
use App\Models\Back\Company;
use App\Models\Back\ExportTraineesToExcelJobTracker;
use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeBlockList;
use App\Models\Back\TraineeGroup;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\InboxMessage;
use App\Models\MaritalStatus;
use App\Notifications\CustomTraineeNotification;
use App\Notifications\TraineeApplicationApprovedNotification;
use App\Notifications\TraineePrivateMessage;
use App\Notifications\TraineeSetupAccountNotification;
use App\Notifications\TraineeWelcomeNotification;
use App\Services\TraineesServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            'trainees' => Trainee::with('company')->with('trainee_group')->latest()->paginate(20),
        ]);
    }

    public function indexArchived()
    {
        return Inertia::render('Back/Trainees/IndexArchived', [
            'blocked_trainees' => Trainee::with('company')
                ->with('trainee_group')
                ->onlyTrashed()
                ->where('suspended_at', null)
                ->latest()
                ->paginate(20),
        ]);
    }

    public function create()
    {
        return Inertia::render('Back/Trainees/Create', [
            'companies' => Company::get(),
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
            'company_id' => 'nullable|exists:companies,id',
            'trainee_group_name' => 'nullable|string|max:255',
            'email' => 'string|max:255|unique:trainees,email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:trainees,phone',
            'phone_additional' => 'nullable|string|max:255|unique:trainees,phone_additional',
            'identity_number' => 'required|string|max:255|unique:trainees,identity_number',
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
            'companies' => Company::get(),
            'trainee' => Trainee::with(['company', 'educational_level', 'city', 'marital_status', 'trainee_group', 'user'])->findOrFail($id),
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
     * @return
     */
    public function storeNationalAddress(Request $request, $trainee_id)
    {
        $request->validate([
            'national_address_copy' => 'required_without:file',
            'file' => 'required_without:national_address_copy',
        ]);

        $trainee = Trainee::findOrFail($trainee_id);
        $file = $request->file('national_address_copy') ?: $request->file('file');
        $uploaded_file = $trainee->uploadToFolder($file, 'national-address');

        // When the other has been filled, mark the application as pending approval from the administration.
        if ($trainee->national_address_copy) {
            $trainee->status = Trainee::STATUS_PENDING_APPROVAL;
            $trainee->save();
        }

        return $uploaded_file;
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return
     */
    public function storeCv(Request $request, $trainee_id)
    {
        $request->validate([
            'cv' => 'required_without:file',
            'file' => 'required_without:cv',
        ]);

        $trainee = Trainee::findOrFail($trainee_id);
        $file = $request->file('cv') ?: $request->file('file');
        $uploaded_file = $trainee->uploadToFolder($file, 'cv');

        // When the other has been filled, mark the application as pending approval from the administration.
        if ($trainee->cv_url) {
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
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteNationalAddress(Request $request, $trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);
        $trainee->getMedia('national-address')->each->forceDelete();

        $trainee->status = Instructor::STATUS_PENDING_UPLOADING_FILES;
        $trainee->save();

        return response()->redirectToRoute('back.trainees.show', $trainee->id);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCv(Request $request, $trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);
        $trainee->getMedia('cv')->each->forceDelete();

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
        if (filter_var(request()->load_trainees, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
            $groups = TraineeGroup::with(['trainees' => function($q) {
                if (request()->company_id) {
                    return $q->where('company_id', request()->company_id);
                }
            }])
                ->get()
                ->toArray();
        } else {
            $groups = TraineeGroup::get()
                ->toArray();
        }

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
            'phone' => $trainee->phone,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        Notification::send($user, new TraineeSetupAccountNotification());

        $message = new InboxMessage();
        $message->body = 'لقد تم قبولك في منصة التدريب';
        $message->to_id = $user->id;
        $message->is_system_message = true;
        $message->save();

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
            'national_address_copy' => 'required',
            'cv' => 'required',
        ]);

        $this->storeIdentity($request, auth()->user()->trainee->id);
        $this->storeQualification($request, auth()->user()->trainee->id);
        $this->storeBankAccount($request, auth()->user()->trainee->id);
        $this->storeNationalAddress($request, auth()->user()->trainee->id);
        $this->storeCv($request, auth()->user()->trainee->id);

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

        Notification::send($trainee->user ?: $trainee, new TraineeApplicationApprovedNotification());

        Log::info('Trainee ID: '.$trainee->id.' has been approved by user: '.auth()->user()->email);

        return redirect()->route('back.trainees.show', $trainee->id);
    }

    /**
     * Update trainee data.
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainee_id
     * @return \Illuminate\Http\RedirectResponse|string
     * @throws \Throwable
     */
    public function update(Request $request, $trainee_id)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'trainee_group_name' => 'nullable|string|max:255',
            'email' => 'required|string|max:255|unique:trainees,email,'.$trainee_id,
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:255',
            'birthday' => 'nullable|date',
            'educational_level_id' => 'nullable|exists:educational_levels,id',
            'city_id' => 'nullable|exists:cities,id',
            'marital_status_id' => 'nullable|exists:marital_statuses,id',
        ]);

        $request->validate([
            'identity_number' => ['required', 'unique:instructors'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:instructors'],
            'phone' => ['required', 'string', 'max:255', 'unique:instructors'],
        ]);

        $trainee = Trainee::findOrFail($trainee_id);

        if ($trainee->user) {
            $request->validate([
                'email' => 'required|string|max:255|unique:users,email,'.$trainee->user->id,
            ]);
        }

        DB::beginTransaction();
        $trainee->update($request->except('_token'));
        if ($user = $trainee->user) {
            $user->email = $trainee->refresh()->email;
            $user->save();
        }

        if ($request->trainee_group_name) {
            $group = TraineeGroup::firstOrCreate(['name' => $request->trainee_group_name]);
            $trainee->trainee_group_id = $group->id;
            $trainee->save();
        } else {
            $trainee->trainee_group_id = null;
            $trainee->save();
        }

        DB::commit();

        return redirect()->route('back.trainees.show', $trainee_id);
    }

    public function blockView($trainee_id) {

        $trainee = Trainee::findOrFail($trainee_id);
        return Inertia::render('Back/Trainees/Block', [
            'trainee' => $trainee,
        ]);

    }

    public function showBlocked($id)
    {
        return Inertia::render('Back/Trainees/ShowBlocked', [
            'trainee' => Trainee::with(['educational_level', 'city', 'marital_status', 'trainee_group'])->onlyTrashed()->findOrFail($id),
            'trainee_groups' => TraineeGroup::get(),
            'cities' => City::orderBy('name_ar')->get(),
            'marital_statuses' => MaritalStatus::orderBy('order')->get(),
            'educational_levels' => EducationalLevel::orderBy('order')->get(),
        ]);
    }


    public function unblock(Request $request, $trainee_id)
    {
        $trainee = Trainee::onlyTrashed()->findOrFail($trainee_id);
        $trainee->suspended_at = null;
        $trainee->save();
        $trainee->restore();
        $blockList = TraineeBlockList::where('trainee_id', $trainee->id)->first();
        if ($blockList) {
            $blockList->delete();
        }
        return redirect()->route('back.trainees.show', $trainee_id);
    }

    public function block(Request $request, $trainee_id)
    {
        DB::beginTransaction();
        $trainee = Trainee::findOrFail($trainee_id);
        $trainee->update([
            'deleted_remark' => $request->deleted_remark,
            ]);
        $trainee->delete();
        if ($trainee->user) {
            $trainee->user->delete();
        }
        DB::commit();
        return redirect()->route('back.trainees.index');
    }

    public function suspendCreate($trainee_id)
    {
        return Inertia::render('Back/Trainees/Suspend', [
            'trainee' => Trainee::findOrFail($trainee_id),
        ]);
    }

    public function suspendEdit($trainee_block_list_id)
    {
        return Inertia::render('Back/Trainees/BlockedList/Edit', [
            'traineeBlockList' => TraineeBlockList::with('trainee')->findOrFail($trainee_block_list_id),
        ]);
    }

    public function suspendUpdate($trainee_block_list_id, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
        ]);

        $traineeBlockList = TraineeBlockList::findOrFail($trainee_block_list_id);
        $traineeBlockList->update($request->except('_token'));
        if ($traineeBlockList->trainee) {
            $traineeBlockList->trainee->deleted_remark = $request->reason;
            $traineeBlockList->trainee->save();
        }

        return redirect()->route('back.trainees.block-list.index');
    }

    public function suspend(Request $request, $trainee_id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        $trainee = Trainee::findOrFail($trainee_id);
        $trainee->deleted_remark = $request->reason;
        $trainee->suspended_at = now()->setSecond(0);
        $trainee->save();
        $block = TraineeBlockList::create([
            'trainee_id' => $trainee->id,
            'identity_number' => $trainee->identity_number,
            'name' => $trainee->name,
            'email' => $trainee->email,
            'phone' => $trainee->phone,
            'phone_additional' => $trainee->phone_additional,
            'reason' => $request->reason,
        ]);
        if ($trainee->user) {
            $trainee->user->delete();
        }
        $trainee->delete();
        DB::commit();
        return redirect()->route('back.trainees.block-list.index');
    }

    public function excel(Request $request)
    {
        $request->validate([
            'trainee_status_id' => 'nullable|numeric',
        ]);

        $validate = in_array((int) $request->trainee_status_id, [
            Trainee::STATUS_PENDING_APPROVAL,
            Trainee::STATUS_APPROVED,
            Trainee::STATUS_PENDING_UPLOADING_FILES,
            null,
        ], true);

        throw_if(!$validate, 'Unknown trainee status');

        $excelJob = new ExportTraineesToExcelJobTracker();
        $excelJob->trainee_status_id = $request->trainee_status_id;
        $excelJob->queued_at = now();
        $excelJob->user_id = auth()->user()->id;
        $excelJob->team_id = auth()->user()->current_team_id;
        $excelJob->save();

        dispatch(new ExportTraineesToExcelJob($excelJob));

        return $excelJob;
    }

    /**
     *
     * @param $id
     * @return mixed
     */
    public function excelJob($id)
    {
        return ExportTraineesToExcelJobTracker::find($id);
    }

    public function excelJobDownload($id)
    {
        $tracker = ExportTraineesToExcelJobTracker::find($id);
        $file = $tracker->getFirstMedia('excel');
        if ($file->disk === 's3') {
            return redirect()->to($file->getTemporaryUrl(now()->addMinutes(5), '', [
                //'ResponseContentType' => 'application/octet-stream',
            ]));
        } else {
            return response()->download($file->getPath());
        }
    }

    public function archivedExcel()
    {

        $excelJob = new ExportTraineesToExcelJobTracker();
        $excelJob->queued_at = now();
        $excelJob->user_id = auth()->user()->id;
        $excelJob->team_id = auth()->user()->team_id;
        $excelJob->save();

        dispatch(new ExportArchivedTraineesToExcelJob($excelJob));

        return $excelJob;
    }


    public function resendInvitation($trainee_id)
    {
        $trainee = Trainee::findOrFail($trainee_id);

        if (!$trainee->user) {
            throw new \RuntimeException('There is no user created for this trainee to send an invite to.');
        }

        Notification::send($trainee->user, new TraineeSetupAccountNotification());

        return redirect()->route('back.trainees.show', $trainee_id);
    }

    /**
     * Assign a password for the trainee's account.
     *
     * @param $trainee_id
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function setPassword($trainee_id, Request $request)
    {
        $request->validate([
            'new_trainee_password' => 'required|string|max:56',
        ]);
        \DB::beginTransaction();
        $user = Trainee::findOrFail($trainee_id)->user;
        $user->password = Hash::make($request->new_trainee_password);
        $user->save();
        \DB::Commit();

        return redirect()->route('back.trainees.show', $trainee_id);
    }

    public function sendNotificationForm()
    {
        $this->authorize('send-messages-to-groups-of-trainees');
        return Inertia::render('Back/Trainees/SendNotificationForm');
    }

    public function sendNotification(Request $request)
    {
        $this->authorize('send-messages-to-groups-of-trainees');

        $request->validate([
            'registered_today_only' => 'nullable|boolean',
            'to_trainees_status' => 'required',
            'email_title' => 'nullable|string|max:255',
            'email_body' => 'nullable|string|max:500',
            'sms_body' => 'nullable|string|max:500',
        ]);

        if ($request->to_trainee_status === 'linked_to_company_and_contract') {
            $trainees = Trainee::where('company_id', '!=', null)
                ->where('trainee_group_id', '!=', null)
                ->where('instructor_id', '!=', null);
        } else {
            $trainees = Trainee::where('status', $request->to_trainees_status);
        }

        if ($request->registered_today_only) {
            $trainees->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
        }

        $trainees = $trainees->get();

        Notification::send($trainees,
            new CustomTraineeNotification($request->email_title, $request->email_body, $request->sms_body)
        );

        return redirect()->route('back.trainees.index');
    }

    public function sendPrivateNotificationForm($id)
    {
        return Inertia::render('Back/Trainees/PrivateNotifications/Create', [
            'trainee' => Trainee::findOrFail($id),
        ]);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendPrivateNotification(Request $request, $id)
    {
        $request->validate([
            'email_title' => 'required|string|max:500',
            'email_body' => 'required|string|max:500',
            'sms_body' => 'nullable|string|max:500',
        ]);
        $trainee = Trainee::findOrFail($id);
        $trainee->notify(new TraineePrivateMessage($request->email_title, $request->email_body, $request->sms_body));
        Log::info([
            'Sending message to: '.$trainee->email,
            'Body: '.$request->email_body,
        ]);
        return redirect()->route('back.trainees.show', $trainee->id);
    }

    public function warnings($trainee_id)
    {
        return AttendanceReportRecordWarning::where('trainee_id', $trainee_id)
            ->with(['attendance_report_record' => function($q) {
                $q->with(['course_batch_session' => function($q) {
                    $q->with(['course' => function($q) {
                        $q->with('instructor');
                    }]);
                }]);
            }])
            ->get();
    }

    /**
     * Deletes a warning.
     *
     * @param $trainee_id
     * @param $id
     * @return mixed
     */
    public function warningDelete($trainee_id, $id)
    {
        return AttendanceReportRecordWarning::find($id)
            ->delete();
    }

    public function updatedDeletedRemark($trainee_id, Request $request)
    {
        $request->validate([
            'deleted_remark' => 'nullable|string|max:255',
        ]);

        $t = Trainee::withTrashed()->find($trainee_id);
        $t->deleted_remark = $request->deleted_remark;
        $t->save();

        return $t;
    }
}
