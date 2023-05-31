<?php

namespace App\Http\Controllers;

use App\Models\Back\CompanyAttendanceReport;
use App\Models\NewEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class NewEmailController extends Controller
{
    public function index()
    {
        {
            return Inertia::render('NewEmail/Index');
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

//        Mail::to(ComplaintsSettings::first()->emails)
//            ->queue(new ComplaintMail($new_email));

        return redirect()->route('new_email.orders');
    }

    public function orders(){

        $reports = QueryBuilder::for(NewEmail::class)
            ->allowedSorts(['number'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('NewEmail/Orders', [
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

    public function approveMail(Request $request) {
        \DB::beginTransaction();
        $mails = NewEmail::whereIn('id', $request->new_mails);

                foreach ($mails as $mail) {
                    $mail->status = NewEmail::STATUS_APPROVED;
                    $mail->save();
                }
        \DB::commit();
        return redirect()->route('new_email.orders');
    }
}
