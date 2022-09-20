<?php

namespace App\Http\Controllers\Back;

use App\Events\TraineeAttachedToCompany;
use App\Http\Controllers\Controller;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Models\Back\TraineesComplaint;
use App\Scope\TeamScope;
use App\Services\TraineeCompanyMovementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        //$validatedData = $this->validateStoreRequest($request, $trainee_complaints->id);

        $complaints = TraineesComplaint::create($request->toArray());

       return redirect()->route('complaints.NewComplaints.Show', $complaints->trainee_id);
    }

    public function NewComplaintsShow()
    {
        $this->authorize('view_complaints');

        $complaints = QueryBuilder::for(TraineesComplaint::class)
            ->with(['trainee' => function($q) {
                $q->with('company');
            }])
            ->with('company')
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'number'])
            ->allowedFields(['trainee.identity_number', 'trainee.phone', 'trainee.id', 'trainee.name', 'company.id', 'company.name_ar'])
            ->allowedIncludes(['company', 'trainee'])
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
        $this->authorize('view_complaints');

        $complaints = QueryBuilder::for(TraineesComplaint::class)
            ->with(['trainee' => function($q) {
                $q->with('company');
            }])
            ->with('company')
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'number'])
            ->allowedFields(['trainee.identity_number', 'trainee.phone', 'trainee.id', 'trainee.name', 'company.id', 'company.name_ar'])
            ->allowedIncludes(['company', 'trainee'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Complaints/InProgress/Show', [
            'trainees_complaints' => $complaints,
        ])->table(function ($table) {
            $table->disableGlobalSearch();
        });
    }

    public function DoneComplaintsShow()
    {
        $this->authorize('view_complaints');

        $complaints = QueryBuilder::for(TraineesComplaint::class)
            ->with(['trainee' => function($q) {
                $q->with('company');
            }])
            ->with('company')
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'number'])
            ->allowedFields(['trainee.identity_number', 'trainee.phone', 'trainee.id', 'trainee.name', 'company.id', 'company.name_ar'])
            ->allowedIncludes(['company', 'trainee'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Complaints/DoneComplaints/Show', [
            'trainees_complaints' => $complaints,
        ])->table(function ($table) {
            $table->disableGlobalSearch();
        });
    }

    public function NewToInProgressStatus($id)
    {

        $complaints = TraineesComplaint::findOrFail($id);
        $complaints->complaints_status = TraineesComplaint::COMPLAINTS_STATUS_IN_PROGRESS;
        $complaints->created_by_id = auth()->user()->id;;
        $complaints->save();

        return redirect()->route('complaints.NewComplaints.Show');
    }

    public function InProgressToDoneStatus($id)
    {

        $complaints = TraineesComplaint::findOrFail($id);
        $complaints->complaints_status = TraineesComplaint::COMPLAINTS_STATUS_DONE;
        $complaints->created_by_id = auth()->user()->id;;
        $complaints->save();

        return redirect()->route('complaints.InProgress.Show');
    }

    public function DoneToInProgressStatus($id)
    {

        $complaints = TraineesComplaint::findOrFail($id);
        $complaints->complaints_status = TraineesComplaint::COMPLAINTS_STATUS_NEW;
        $complaints->created_by_id = auth()->user()->id;;
        $complaints->save();

        return redirect()->route('complaints.DoneComplaints.Show');
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
