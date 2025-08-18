<?php

namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use App\Models\Back\CourseBatch;
use App\Models\Back\CourseBatchSession;
use App\Models\Back\GlobalMessages;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user=Auth::user();
        $trainee=Trainee::where('user_id',$user->id)->first();
        

        // $agreement = TraineeAgreement::where('trainee_id', $trainee->id)->first();
        // if (!$agreement || is_null($agreement->accepted_at)) {
        //     return redirect()->route('agreement.show');
        // }


        $instructor = optional(auth()->user()->trainee)->instructor;
        if ($instructor) {
            $coursesIds = Course::where('instructor_id', $instructor->id)->pluck('id');

            $courseBatchesIds = CourseBatch::whereIn('course_id', $coursesIds)
                ->where('trainee_group_id', optional(auth()->user()->trainee)->trainee_group_id)
                ->pluck('id');

            $sessions = CourseBatchSession::whereIn('course_id', $coursesIds)
                ->whereIn('course_batch_id', $courseBatchesIds)
                ->with(['course_batch' => function($q) {
                    $q->with(['course' => function($q) {
                        $q->with('instructor');
                    }]);
            }])->where('starts_at', '>=', now()->startOfDay())
                ->latest()
                ->paginate(15);
        } else {
            $sessions = [];
        }

        if (session()->has('success_payment')) {
            session()->forget('success_payment');
            $show_success_payment = true;
        } else {
            $show_success_payment = false;
        }

        if (session()->has('failed_payment')) {
            session()->forget('failed_payment');
            $show_failed_payment = true;
        } else {
            $show_failed_payment = false;
        }

        $class_timings = optional(auth()->user()->trainee->trainee_group)->class_timings;

        $global_messages = GlobalMessages::where('company_id', auth()->user()->trainee->company_id)
            ->orWhere('company_id', null)
            ->available()
            ->latest()
            ->get();

        // Debug: Log trainee data
        \Log::info('Trainee data for dashboard:', [
            'user_id' => $user->id,
            'trainee_id' => $trainee ? $trainee->id : null,
            'english_name' => $trainee ? $trainee->english_name : null,
            'trainee_exists' => $trainee ? true : false
        ]);

        return Inertia::render('Trainees/Dashboard', [
            'user' => auth()->user(),
            'sessions' => $sessions,
            'show_success_payment' => $show_success_payment,
            'show_failed_payment' => $show_failed_payment,
            'class_timings' => $class_timings,
            'global_messages' => $global_messages,
            'trainee' => $trainee
        ]);
    }
}
