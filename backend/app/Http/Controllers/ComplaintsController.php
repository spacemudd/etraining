<?php
/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Http\Controllers;

use App\Mail\ComplaintMail;
use App\Models\Back\ComplaintsSettings;
use App\Models\Back\TraineesComplaint;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class ComplaintsController extends Controller
{
    public function index()
    {
        $this->authorize('view_complaints');

        $complaints = QueryBuilder::for(TraineesComplaint::class)
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at'])
            ->allowedFields(['course_name', 'course_instructor', 'message', 'created_at'])
            ->allowedIncludes(['complaints'])
            ->paginate()
            ->withQueryString();


        return Inertia::render('Complaints/Index', [
            'complaints' => $complaints,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addSearchRows([
                'course_name' => __('words.course-name'),
                'course_instructor ' => __('words.course-instructor'),
                'message' => __('words.message'),
                'created_at' => __('words.created_at'),
            ]);
        });
    }

    public function show()
    {
        return Inertia::render('Complaints/Dashboard');
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_instructor' => 'required|string|max:255',
            'message' => 'required|string:max:500',
        ]);

        $complaint = Complaint::create([
            'team_id' => auth()->user()->currentTeam()->first()->id,
            'course_name' => $request->course_name,
            'course_instructor' => $request->course_instructor,
            'message' => $request->message,
            'created_by_id' => auth()->user()->id,
        ]);

//        Mail::to(ComplaintsSettings::first()->emails)
//            ->queue(new ComplaintMail($complaint));

        return redirect()->route('trainees-complaints.index');
    }
}
