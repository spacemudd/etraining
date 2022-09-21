<?php

namespace App\Http\Controllers\Back;

use App\Events\TraineeAttachedToCompany;
use App\Http\Controllers\Controller;
use App\Jobs\InvoicesSheetReportJob;
use App\Models\Back\AccountingLedgerBook;
use App\Models\Back\Company;
use App\Models\Back\Invoice;
use App\Models\Back\Trainee;
use App\Models\Back\TraineesComplaint;
use App\Models\JobTracker;
use App\Reports\InvoicesReportFactory;
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
            ->allowedFilters(['trainee.identity_number', 'trainee.name', 'company.name_ar', 'created_at', 'number', 'complaints'])
            ->allowedFields(['trainee.identity_number', 'trainee.phone', 'trainee.id', 'trainee.name', 'company.id', 'company.name_ar', 'complaints'])
            ->allowedIncludes(['company', 'trainee', 'trainees_complaints'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Complaints/NewComplaints/Show', [
            'trainees_complaints' => $complaints,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addSearchRows([
                'number' => __('words.complaint-number'),
                'company.name_ar' => __('words.company'),
                'created_at' => __('words.order-date'),
                'trainee.identity_number' => __('words.identity-number'),
                'trainee.name' => __('words.trainee'),
                'complaints' => __('words.complaints'),
            ]);
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
            ->allowedFilters(['trainee.identity_number', 'trainee.name', 'company.name_ar', 'created_at', 'number', 'complaints'])
            ->allowedFields(['trainee.identity_number', 'trainee.phone', 'trainee.id', 'trainee.name', 'company.id', 'company.name_ar', 'complaints'])
            ->allowedIncludes(['company', 'trainee', 'trainees_complaints'])
            ->paginate()
            ->withQueryString();


        return Inertia::render('Back/Complaints/InProgress/Show', [
            'trainees_complaints' => $complaints,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addSearchRows([
                'number' => __('words.complaint-number'),
                'company.name_ar' => __('words.company'),
                'created_at' => __('words.order-date'),
                'trainee.identity_number' => __('words.identity-number'),
                'trainee.name' => __('words.trainee'),
                'complaints' => __('words.complaints'),
            ]);
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
            ->allowedFilters(['trainee.identity_number', 'trainee.name', 'company.name_ar', 'created_at', 'number', 'complaints'])
            ->allowedFields(['trainee.identity_number', 'trainee.phone', 'trainee.id', 'trainee.name', 'company.id', 'company.name_ar', 'complaints'])
            ->allowedIncludes(['company', 'trainee', 'trainees_complaints'])
            ->paginate()
            ->withQueryString();


        return Inertia::render('Back/Complaints/DoneComplaints/Show', [
            'trainees_complaints' => $complaints,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addSearchRows([
                'number' => __('words.complaint-number'),
                'company.name_ar' => __('words.company'),
                'created_at' => __('words.order-date'),
                'trainee.identity_number' => __('words.identity-number'),
                'trainee.name' => __('words.trainee'),
                'complaints' => __('words.complaints'),
            ]);
        });
    }
    public function Show($id)
    {
        $this->authorize('view_complaints');

        $complaint = TraineesComplaint::with([
            'trainee',
            'company',
            'created_by',
        ])->find($id);

        return Inertia::render('Back/Complaints/Show', [
            'complaint' => $complaint,
        ]);
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
        $complaints->complaints_status = TraineesComplaint::COMPLAINTS_STATUS_IN_PROGRESS;
        $complaints->created_by_id = auth()->user()->id;;
        $complaints->save();

        return redirect()->route('complaints.DoneComplaints.Show');
    }

    public function excel(): \Inertia\Response
    {
        $this->authorize('view_complaints');
        return Inertia::render('Back/Complaints/Excel', [
            'companies' => Company::orderBy('name_ar')->get(),
        ]);
    }

    public function generateExcel(Request $request)
    {
//        $request->validate([
//            'date_from' => 'required',
//            'date_to' => 'required',
//        ]);

        $tracker = new JobTracker();
        $tracker->user_id = auth()->user()->id;
        $tracker->metadata = $request->except('_token');
        $tracker->reportable_id = null;
        $tracker->reportable_type = InvoicesReportFactory::class;
        $tracker->queued_at = now();
        $tracker->save();

        $tracker = $tracker->refresh();

        InvoicesSheetReportJob::dispatch($tracker);

        return $tracker;
    }

    public function destroy()
    {

    }
}
