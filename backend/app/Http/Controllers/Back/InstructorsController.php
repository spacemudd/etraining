<?php

namespace App\Http\Controllers\Back;

use App\Actions\Fortify\CreateNewInstructorUser;
use App\Http\Controllers\Controller;
use App\Models\Back\Instructor;
use App\Models\City;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InstructorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Back/Instructors/Index', [
            'instructors' => Instructor::paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Back/Instructors/Create', [
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

        $instructor = Instructor::create($request->except('_token'));
        return redirect()->route('back.instructors.show', $instructor->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Inertia::render('Back/Instructors/Show', [
            'instructor' => Instructor::with('city')->findOrFail($id),
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
     * @param $instructor_id
     * @return
     */
    public function storeCvFull(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);

        $instructor = Instructor::findOrFail($email);
        $file = $request->file('file');
        return $instructor->uploadToFolder($file, 'cv-full');
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $instructor_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCvFull(Request $request, $instructor_id)
    {
        $instructor = Instructor::findOrFail($instructor_id);
        $instructor->getMedia('cv-full')->each->forceDelete();
        return response()->redirectToRoute('back.instructors.show', $instructor->id);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $instructor_id
     * @return
     */
    public function storeCvSummary(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);

        $instructor = Instructor::findOrFail($email);
        $file = $request->file('file');
        return $instructor->uploadToFolder($file, 'cv-summary');
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $instructor_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCvSummary(Request $request, $instructor_id)
    {
        $instructor = Instructor::findOrFail($instructor_id);
        $instructor->getMedia('cv-summary')->each->forceDelete();
        return response()->redirectToRoute('back.instructors.show', $instructor->id);
    }

    /**
     * Open a new account for the instructor where they can login with it.
     *
     */
    public function createUser($instructor_id)
    {
        $instructor = Instructor::findOrFail($instructor_id);
        $user = (new CreateNewInstructorUser())->create([
            'instructor_id' => $instructor->id,
            'name' => $instructor->name,
            'email' => $instructor->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        return redirect()->route('back.instructors.show', $instructor->id);
    }
}
