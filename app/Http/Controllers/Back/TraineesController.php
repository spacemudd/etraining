<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
use App\Models\Media;
use Illuminate\Http\Request;
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
            'cities' => City::get(),
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
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:255',
            'birthday' => 'required|date',
            'educational_level_id' => 'nullable|exists:educational_levels,id',
            'city_id' => 'nullable|exists:cities,id',
            'marital_status_id' => 'nullable|exists:marital_statuses,id',
            'children_count' => 'nullable|integer',
        ]);

        $trainee = Trainee::create($request->except('_token'));

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
}
