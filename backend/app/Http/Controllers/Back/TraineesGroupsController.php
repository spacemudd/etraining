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
            ->orderBy('name')
            ->get();

            foreach ($traineeGroups as $traineeGroup) {
                $traineeGroup->load(['trainees' => function($q) {
                    $q->whereHas('company', function($query) {
                        $query->whereNull('deleted_at'); 
                    })->limit(1);
                }]);
            }
        return Inertia::render('Back/TraineeGroups/Index', [
            'traineeGroups' => $traineeGroups,
        ]);
    }


  
    
}
