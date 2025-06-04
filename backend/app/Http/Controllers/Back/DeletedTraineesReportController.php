<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Excel;

class DeletedTraineesReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Reports/DeletedTrainees/Index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $trainees = Trainee::onlyTrashed()
            ->whereBetween('deleted_at', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
            ])
            ->with(['company', 'deleted_by'])
            ->get();

        $data = [];
        $data[] = [
            'Name',
            'Company',
            'Email',
            'Phone',
            'Deleted At',
            'Deleted By',
            'Delete Reason',
            'Posted At',
        ];

        foreach ($trainees as $trainee) {
            $data[] = [
                $trainee->name,
                optional($trainee->company)->name_ar,
                $trainee->email,
                $trainee->phone,
                $trainee->deleted_at ? $trainee->deleted_at->format('Y-m-d H:i:s') : '',
                optional($trainee->deleted_by)->name,
                $trainee->deleted_remark,
                $trainee->posted_at ? Carbon::parse($trainee->posted_at)->format('Y-m-d H:i:s') : '',
            ];
        }

        return Excel::download(new \App\Exports\DeletedTraineesReport($data), 'deleted-trainees-report.xlsx');
    }
} 