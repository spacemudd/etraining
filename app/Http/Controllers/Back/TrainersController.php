<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Models\Back\Trainer;
use App\Models\City;
use App\Models\EducationalLevel;
use App\Models\MaritalStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Back/Trainers/Index', [
            'trainers' => Trainer::paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Back/Trainers/Create', [
            'cities' => City::orderBy('name_ar')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email',
            'twitter_link' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $trainer = Trainer::create($request->except('_token'));
        return redirect()->route('back.trainers.show', $trainer->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Inertia::render('Back/Trainers/Show', [
            'trainer' => Trainer::with('city')->findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainer_id
     * @return
     */
    public function storeCvFull(Request $request, $trainer_id)
    {
        $request->validate([
            'file' => 'required',
        ]);

        $trainer = Trainer::findOrFail($trainer_id);
        $file = $request->file('file');
        return $trainer->uploadToFolder($file, 'cv-full');
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainer_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCvFull(Request $request, $trainer_id)
    {
        $trainer = Trainer::findOrFail($trainer_id);
        $trainer->getMedia('cv-full')->each->forceDelete();
        return response()->redirectToRoute('back.trainers.show', $trainer->id);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainer_id
     * @return
     */
    public function storeCvSummary(Request $request, $trainer_id)
    {
        $request->validate([
            'file' => 'required',
        ]);

        $trainer = Trainer::findOrFail($trainer_id);
        $file = $request->file('file');
        return $trainer->uploadToFolder($file, 'cv-summary');
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $trainer_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCvSummary(Request $request, $trainer_id)
    {
        $trainer = Trainer::findOrFail($trainer_id);
        $trainer->getMedia('cv-summary')->each->forceDelete();
        return response()->redirectToRoute('back.trainers.show', $trainer->id);
    }
}
