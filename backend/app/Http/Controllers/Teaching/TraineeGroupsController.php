<?php

namespace App\Http\Controllers\Teaching;

use App\Http\Controllers\Controller;
use App\Models\Back\TraineeGroup;
use Inertia\Inertia;

class TraineeGroupsController extends Controller
{
    public function index()
    {
        $traineeGroups = TraineeGroup::with(['trainees' => function($q) {
            $q->responsibleToTeach();
        }])->whereHas('trainees', function($q) {
            $q->responsibleToTeach();
        })
            ->get();

        return Inertia::render('Teaching/TraineeGroups/Index', [
            'traineeGroups' => $traineeGroups,
        ]);
    }
}
