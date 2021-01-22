<?php

namespace App\Http\Controllers\Back;

use App\Actions\Fortify\CreateNewInstructorUser;
use App\Http\Controllers\Controller;
use App\Models\Back\Instructor;
use App\Models\City;
use App\Notifications\InstructorApplicationApprovedNotification;
use App\Notifications\InstructorWelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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
            'blocked_instructors' => Instructor::onlyTrashed()->paginate(20),
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
            'cities' => City::orderBy('name_ar')->get()
        ]);
    }

    public function showBlocked($id)
    {
        return Inertia::render('Back/Instructors/ShowBlocked', [
            'instructor' => Instructor::onlyTrashed()->with('city')->findOrFail($id),
            'cities' => City::orderBy('name_ar')->get()
        ]);
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
     * This is different from both functions below. Because, in this way, we can allow the use of different functions
     * without modifying them. So the admin can still use them!
     *
     * Notice that it will use the other two functions as well. However, this one is intended to return an Inertia page
     * which is the email landing page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function storeCvFromApplication(Request $request)
    {
        $request->validate([
            'cv_full' => 'required',
            'cv_summary' => 'required',
        ]);

        $this->storeCvFull($request, auth()->user()->instructor->id);
        $this->storeCvSummary($request, auth()->user()->instructor->id);

        $instructor = auth()->user()->instructor;
        $instructor->status = Instructor::STATUS_PENDING_APPROVAL;
        $instructor->save();

        Notification::send(auth()->user(), new InstructorWelcomeNotification());

        return $instructor->media;
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $instructor_id
     * @return mixed
     */
    public function storeCvFull(Request $request, $instructor_id)
    {
        $request->validate([
            'cv_full' => 'nullable',
            'file' => 'nullable',
        ]);

        $instructor = Instructor::findOrFail($instructor_id);
        $file = $request->file('cv_full') ?: $request->file('file');
        $uploaded_file = $instructor->uploadToFolder($file, 'cv-full');

        // When the other has been filled, mark the application as pending approval from the administration.
        if ($instructor->cv_summary_copy_url) {
            $instructor->status = Instructor::STATUS_PENDING_APPROVAL;
            $instructor->save();
        }

        return $uploaded_file;
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

        $instructor->status = Instructor::STATUS_PENDING_UPLOADING_FILES;
        $instructor->save();

        return response()->redirectToRoute('back.instructors.show', $instructor->id);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @param $instructor_id
     * @return
     */
    public function storeCvSummary(Request $request, $instructor_id)
    {
        $request->validate([
            'cv_summary' => 'nullable',
            'file' => 'nullable',
        ]);

        $instructor = Instructor::findOrFail($instructor_id);
        $file = $request->file('cv_summary') ?: $request->file('file');
        $media_file = $instructor->uploadToFolder($file, 'cv-summary');

        // When the other has been filled, mark the application as pending approval from the administration.
        if ($instructor->cv_full_copy_url) {
            $instructor->status = Instructor::STATUS_PENDING_APPROVAL;
            $instructor->save();
        }

        return $media_file;
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

        $instructor->status = Instructor::STATUS_PENDING_UPLOADING_FILES;
        $instructor->save();

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

    /** Update the specified model
     * @param \Illuminate\Http\Request $request
     * @param $instructor_id
     * @return model
    */

    public function update(Request $request, $instructor_id)
    {
        $instructor = Instructor::findOrFail($instructor_id);
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'identity_number' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'city_id' => 'nullable|string|max:255',
            'twitter_link' => 'nullable|string|max:255',
        ]);

         $instructor->update(
            [
                "name" => $request->instructor['name'],
                "phone" => $request->instructor['phone'],
                "identity_number" => $request->instructor['identity_number'],
                "email" => $request->instructor['email'],
                "city_id" => $request->instructor['city_id'],
                "twitter_link" => $request->instructor['twitter_link']
            ]
        );

        return redirect()->route('back.instructors.show', $instructor_id);
    }

    /**
     * Approving the instructor to use the platform and start broadcasting.
     *
     * @param $instructor_id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function approveUser($instructor_id)
    {
        //$this->authorize('approve-instructor-applicants');

        $instructor = Instructor::findOrFail($instructor_id);
        $instructor->status = Instructor::STATUS_APPROVED;
        $instructor->approved_by_id = auth()->user()->id;
        $instructor->approved_at = now();
        $instructor->save();

        Notification::send($instructor->user, new InstructorApplicationApprovedNotification());

        Log::info('Instructor ID: '.$instructor->id.' has been approved by user: '.auth()->user()->email);

        return redirect()->route('back.instructors.show', $instructor->id);
    }

    public function block(Request $request, $instructor_id)
    {
        DB::beginTransaction();
        $instructor = Instructor::findOrFail($instructor_id);
        $instructor->delete();
        if($instructor->user) {
            $instructor->user->delete();
        }
        DB::commit();

        return redirect()->route('back.instructors.index');
    }

    public function unblock(Request $request, $instructor_id)
    {
        $instructor = Instructor::onlyTrashed()->findOrFail($instructor_id)->restore();
        return redirect()->route('back.instructors.show', $instructor_id);
    }
}
