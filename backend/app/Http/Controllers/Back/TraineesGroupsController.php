<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\TraineeGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TraineesGroupsController extends Controller
{
    public function index()
    {
        $traineeGroups = TraineeGroup::withCount('trainees')
            ->with(['trainees' => function($q) {
                $q->limit(1);
            }])
            ->get();

        return Inertia::render('Back/TraineeGroups/Index', [
            'traineeGroups' => $traineeGroups,
        ]);
    }
}
