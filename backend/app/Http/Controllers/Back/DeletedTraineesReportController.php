<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\Trainee;
use App\Exports\DeletedTraineesReport;
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
            ->with(['company', 'deleted_by'])
            ->whereBetween('deleted_at', [
                Carbon::parse($request->date_from)->startOfDay(),
                Carbon::parse($request->date_to)->endOfDay()
            ])
            ->get();

        $data = [
            ['Name', 'Company', 'Email', 'Phone', 'Deleted At', 'Deleted By', 'Deletion Reason', 'Posted At'],
        ];

        foreach ($trainees as $trainee) {
            $data[] = [
                $trainee->name,
                optional($trainee->company)->name_ar,
                $trainee->email,
                $trainee->phone,
                $trainee->deleted_at->format('Y-m-d H:i:s'),
                optional($trainee->deleted_by)->name ?? 'N/A',
                $trainee->deleted_remark ?? 'N/A',
                $trainee->posted_at ? Carbon::parse($trainee->posted_at)->format('Y-m-d H:i:s') : 'Not Posted',
            ];
        }

        return Excel::download(
            new DeletedTraineesReport($data),
            'deleted_trainees_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx'
        );
    }
} 