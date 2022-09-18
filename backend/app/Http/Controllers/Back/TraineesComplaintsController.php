<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Invoice;
use App\Models\Complaint;
use App\Models\TraineesComplaint;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class TraineesComplaintsController extends Controller
{
    public function index(): \Inertia\Response
    {
        return Inertia::render('Back/Complaints/Index');
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('Back/Complaints/Create');
    }

    public function store(Request $request)
    {
        $complaint = TraineesComplaint::create($request->except('_token'));
        return $complaint;
    }

    public function NewComplaintsShow()
    {
//        $this->authorize('view_complaints');

        $complaints = QueryBuilder::for(TraineesComplaint::class)
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'number'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Complaints/NewComplaints/Show', [
            'trainees_complaints' => $complaints,
        ])->table(function ($table) {
            $table->disableGlobalSearch();
        });
    }

    public function InProgressShow()
    {
//        $this->authorize('view_complaints');

        return Inertia::render('Back/Complaints/InProgress/Show');
    }

    public function DoneComplaintsShow()
    {
//        $this->authorize('view_complaints');

        return Inertia::render('Back/Complaints/DoneComplaints/Show');
    }

    public function excel()
    {

    }

    public function generateExcel()
    {

    }

    public function destroy()
    {

    }
}
