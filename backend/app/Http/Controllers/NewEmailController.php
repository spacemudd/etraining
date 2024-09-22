<?php

namespace App\Http\Controllers;

use App\Mail\AcceptNewEmailMail;
use App\Mail\RejectNewEmailMail;
use App\Mail\NewEmailMail;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\NewEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class NewEmailController extends Controller
{
    public function index()
    {
        {
            return Inertia::render('Orders/IT/NewEmail/Create');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'applicant' => 'required|string|max:255',
            'personal_email' => 'required|string|max:255',
            'phone' => 'required|string:max:500',
            'job_title' => 'required|string:max:500',
            'manager_name' => 'required|string:max:500',
            'manager_email' => 'required|string:max:500',
            'new_email' => 'required|string:max:500',
        ]);

        $new_email = NewEmail::create([
            'applicant' => $request->applicant,
            'personal_email' => $request->personal_email,
            'phone' => $request->phone,
            'job_title' => $request->job_title,
            'manager_name' => $request->manager_name,
            'manager_email' => $request->manager_email,
            'new_email' => $request->new_email,
            'created_by_id' => auth()->user()->id,
        ]);

        $users = User::permission('view-orders')->get();
        Mail::to($users)
            ->queue(new NewEmailMail(auth()->user()->name));

        return redirect()->route('dashboard');
    }

    public function orders(){

        $reports = QueryBuilder::for(NewEmail::class)
            ->allowedSorts(['number'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Orders/Orders', [
            'new_emails' => $reports,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addFilter('status', __('words.status'), [
                NewEmail::STATUS_PENDING => __('words.pending'),
                NewEmail::STATUS_APPROVED => __('words.approved'),
                NewEmail::STATUS_REJECTED => __('words.rejected'),
            ]);
        });

    }

    public function approveMail($id, Request $request)
    {

        \DB::beginTransaction();
        $email = NewEmail::find($id);
        $email->status = NewEmail::STATUS_APPROVED;
        $email->save();
        \DB::commit();

        Mail::to(['billing@ptc-ksa.net', $email->personal_email])
            ->cc(['ceo@ptc-ksa.net'])
            ->queue(new AcceptNewEmailMail($email));

        return redirect()->route('new_email.orders');
    }
    public function rejectMail($id, Request $request)
    {
        \DB::beginTransaction();
        $email = NewEmail::find($id);
        $email->status = NewEmail::STATUS_REJECTED;
        $email->save();
        \DB::commit();

        Mail::to(['billing@ptc-ksa.net', $email->personal_email] )
            ->cc(['ceo@ptc-ksa.net'])
            ->queue(new RejectNewEmailMail($email));

        return redirect()->route('new_email.orders');
    }
}
