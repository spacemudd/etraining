<?php

namespace App\Http\Controllers\Back;

use App\Events\TraineeAttachedToCompany;
use App\Http\Controllers\Controller;
use App\Models\Back\AccountingLedgerBook;
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
//        $this->authorize('view_complaints');

        $complaints = QueryBuilder::for(TraineesComplaint::class)
            ->with('company')
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

        $complaints = QueryBuilder::for(TraineesComplaint::class)
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'number'])
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
//        $this->authorize('view_complaints');

        $complaints = QueryBuilder::for(TraineesComplaint::class)
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'number'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Back/Complaints/DoneComplaints/Show', [
            'trainees_complaints' => $complaints,
        ])->table(function ($table) {
            $table->disableGlobalSearch();
        });
    }

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->currentTeam()->first()->id;
            }
        });

        static::updating(function ($model) {
            $companyChanged = $model->company_id != $model->getOriginal('company_id');

            if ($companyChanged) {
                TraineeAttachedToCompany::dispatch($model->id, $model->company_id);
                app()->make(TraineeCompanyMovementService::class)
                    ->recordMovement($model->id, $model->company_id, $model->getOriginal('company_id'));
            }

            //$isFinanceUser = (Str::contains('finance', optional(optional(auth()->user())->roles()->first())->name) || Str::contains('chasers', optional(optional(auth()->user())->roles()->first())->name));
            //if ($companyChanged && $model->company_id && !$isFinanceUser) {
            //    $model->notify(new AssignedToCompanyTraineeNotification());
            //}
        });
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
