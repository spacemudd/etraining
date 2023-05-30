<?php

namespace App\Http\Controllers;

use App\Mail\ComplaintMail;
use App\Models\Back\ComplaintsSettings;
use App\Models\Back\Trainee;
use App\Models\Complaint;
use App\Models\NewEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

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
            'phone' => 'required|Integer',
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

        return redirect()->route('dashboard');
    }
}
