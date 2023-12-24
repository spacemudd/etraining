<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Mail\CompanyAttendanceFailureMail;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyMail;
use App\Services\CompaniesService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Mail;

class MailController extends Controller
{
    public function store(Request $request)
    {
        $eventData = $request->input('event-data');

        // tracking delivery of company attendances reports
        if (array_key_exists('company_attendance_report_id', $eventData['user-variables'])) {
            if ($eventData['event'] === 'failed') {

                $email = CompanyAttendanceReport::find($eventData['user-variables']['company_attendance_report_id'])
                    ->emails()
                    ->where('email', $eventData['recipient'])
                    ->first();

                if ($email->failed_at) {
                    // already failed and mailgun is just retrying
                    // (so we don't send an email in order to not annoy people)...
                    $email->update([
                        'failed_at' => now(),
                        'failed_reason' => $eventData['delivery-status']['message'],
                    ]);
                } else {
                    // failed for the first time, email people.
                    $email->update([
                        'failed_at' => now(),
                        'failed_reason' => $eventData['delivery-status']['message'],
                    ]);
                    Mail::to(['sara@ptc-ksa.net', 'samar.h@ptc-ksa.net', 'ceo@ptc-ksa.net', 'billing@ptc-ksa.net'])
                        ->send(new CompanyAttendanceFailureMail($email));
                }

            } elseif ($eventData['event'] === 'delivered') {
                CompanyAttendanceReport::find($eventData['user-variables']['company_attendance_report_id'])
                    ->emails()
                    ->where('email', $eventData['recipient'])
                    ->update([
                        'delivered_at' => now(),
                    ]);
            }

            return true;
        }

        $company = CompaniesService::new()->findByDomainName(Str::after($request->input('sender'), '@'));

        if (!$company) {
            throw new \RuntimeException('No company found to save the mail under.');
        }

        $sender = $request->input('sender'); // mohammad@acme.com
        $from = $request->input('from'); // Mohammad <mohammad@acme.com>
        $subject = $request->input('subject');

        $bodyPlain = $request->input('body-plain');
        $bodyHtml = $request->input('body-html');

        $companyMail = CompanyMail::create([
            'company_id' => $company->id,
            'from' => $from,
            'subject' => $subject,
            'sender' => $sender,
            'body_text' => $bodyPlain,
            'body_html' => $bodyHtml,
        ]);

        if ($request->has('attachments')) {
            $attachments = collect(json_decode($request->input('attachments'), true, 512, JSON_THROW_ON_ERROR));

            // loop through each attachment and save them on the local filesystem.
            $attachments->each(function ($attachment) use ($companyMail, $company) {
                $fileContents = Http::withBasicAuth(
                    config('services.mailgun.domain'),
                    config('services.mailgun.secret')
                )->get(Arr::get($attachment, 'url'));

                $companyMail->addMediaFromString($fileContents)
                    ->usingFileName(Arr::get($attachment, 'name'))
                    ->withAttributes([
                        'team_id' => $company->team_id,
                    ])
                    ->toMediaCollection('attachments');
            });
        }

        return response()->json(['company_mail_id' => $companyMail->id]);
    }

    public function viewCompanyMails($company_id, $id)
    {
        $company_mails = CompanyMail::findOrFail($id);

        return Inertia::render('Back/Companies/Emails/Show', [
            'company' => Company::findOrFail($company_id),
            'company_mails' => $company_mails,
        ]);
    }

}
