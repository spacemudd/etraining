<?php

namespace App\Http\Controllers\Teaching;

use App\Http\Controllers\Controller;
use App\Models\Back\Course;
use App\Models\Back\TraineeGroup;
use App\Models\User;
use App\Notifications\TraineeGroupAnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class TraineeGroupsController extends Controller
{
    public function index()
    {
        $traineeGroups = TraineeGroup::
            withCount('trainees')
            ->get();

        return Inertia::render('Teaching/TraineeGroups/Index', [
            'traineeGroups' => $traineeGroups,
        ]);
    }

    /**
     * An instructor can send an announcement to all
     * the trainees registered under a group.
     *
     * @param $trainee_group_id
     * @return \Inertia\Response
     */
    public function createAnnouncement($trainee_group_id)
    {
        $traineeGroup = TraineeGroup::with(['trainees' => function($q) {
            $q->responsibleToTeach();
        }])->whereHas('trainees', function($q) {
            $q->responsibleToTeach();
        })->findOrFail($trainee_group_id);

        $courses = Course::responsibleToTeach()->get();

        return Inertia::render('Teaching/TraineeGroups/Announcements/Create', [
            'courses' => $courses,
            'traineeGroup' => $traineeGroup,
        ]);
    }

    /**
     * Sending a global announcement for all the trainees
     * registered under a group.
     *
     * @param $trainee_group_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendAnnouncement($trainee_group_id, Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'message' => 'required|string|max:255',
        ]);

        $course = Course::responsibleToTeach()->findOrFail($request->course_id);

        // Abort if the user has sent one already in the day.
        $cache = Cache::get(auth()->user()->email.'-sending-'.$course->id.'-'.$trainee_group_id);

        if ($cache) {
            return __('words.you-can-only-send-a-message-per-day');
        }

        // A 24-hour limit on sending.
        //dd(auth()->user()->email.'-sending-'.$course->id);
        Cache::put(auth()->user()->email.'-sending-'.$course->id.'-'.$trainee_group_id, 'sent', 86400);

        $traineeGroup = TraineeGroup::with(['trainees' => function($q) {
            $q->responsibleToTeach();
        }])->whereHas('trainees', function($q) {
            $q->responsibleToTeach();
        })->findOrFail($trainee_group_id);

        $users = User::whereIn('id', $traineeGroup->trainees->pluck('user_id'));

        $users->chunk(20, function($users) use ($traineeGroup, $course, $request) {
            Notification::send($users, new TraineeGroupAnnouncementNotification($traineeGroup, $course, $request->message));
        });

        Log::info([
            'instructor' => auth()->user()->email,
            'action' => 'Sent an announcement to trainee group',
            'message' => $request->message,
            'ip' => request()->ip(),
            'fingerprint' => request()->fingerprint(),
        ]);

        return redirect()->route('teaching.trainee-groups.index');
    }
}
