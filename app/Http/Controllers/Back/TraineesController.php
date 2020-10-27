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
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TraineesController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Trainees/Index', [
            'trainees' => Trainee::with('company')->paginate(20),
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

        \DB::beginTransaction();
        $trainee = Trainee::create($request->except('_token'));
        if ($request->trainee_group_name) {
            $group = TraineeGroup::firstOrCreate([
                'name' => $request->trainee_group_name,
            ]);
            $group->trainees()->attach([$trainee->id]);
        }
        \DB::commit();

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
            'trainee' => Trainee::with(['educational_level', 'city', 'marital_status'])->findOrFail($id),
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
            'file' => 'required',
        ]);

        $trainee = Trainee::findOrFail($trainee_id);
        $file = $request->file('file');
        return $trainee->uploadToFolder($file, 'identity');
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
            'file' => 'required',
        ]);

        $trainee = Trainee::findOrFail($trainee_id);
        $file = $request->file('file');
        return $trainee->uploadToFolder($file, 'qualification');
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
            'file' => 'required',
        ]);

        $trainee = Trainee::findOrFail($trainee_id);
        $file = $request->file('file');
        return $trainee->uploadToFolder($file, 'bank-account');
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
}
