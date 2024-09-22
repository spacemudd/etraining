<?php

namespace App\Http\Controllers\Teaching;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Models\Back\TraineeGroup;
use App\Models\InboxMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TraineeGroupTraineesController extends Controller
{
    /**
     * Get all trainees under a specific trainee group.
     *
     * @param $trainee_group_id
     * @return \Inertia\Response
     */
    public function index($trainee_group_id)
    {
        $traineeGroup = TraineeGroup::findOrFail($trainee_group_id);
        $trainees = $traineeGroup->trainees()->paginate(30);

        return Inertia::render('Teaching/TraineeGroups/Trainees/Index', [
            'traineeGroup' => $traineeGroup,
            'trainees' => $trainees,
        ]);
    }

    /**
     * Show a trainee.
     *
     * @param $training_group_id
     * @param $id
     * @return \Inertia\Response
     */
    public function show($trainee_group_id, $id)
    {
        return Inertia::render('Teaching/TraineeGroups/Trainees/Show', [
            'traineeGroup' => TraineeGroup::findOrFail($trainee_group_id),
            'trainee' => Trainee::with(['educational_level', 'city', 'marital_status'])->findOrFail($id),
        ]);
    }

    /**
     * Sending a message to trainee.
     *
     * @param $trainee_group_id
     * @param $trainee_id
     * @param \Illuminate\Http\Request $request
     */
    public function sendMessage($trainee_group_id, $trainee_id, Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'trainee_id' => 'required|exists:trainees,id',
            'trainee_group_id' => 'required|exists:trainee_groups,id',
        ]);

        $message = new InboxMessage();
        $message->body = $request->message;
        $message->to_id = Trainee::findOrFail($request->trainee_id)->user_id;
        $message->from_id = auth()->user()->id;
        $message->save();

        return redirect()->route('teaching.trainee-groups.trainees.show', [
            'trainee_group_id' => $request->trainee_group_id,
            'id' => $request->trainee_id,
        ]);
    }
}
