<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\Back\CompanyMail;
use App\Services\CompaniesService;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MailController extends Controller
{
    public function store(Request $request)
    {
        $eventData = $request->input('event-data');

        // tracking delivery of company attendances reports
        if (array_key_exists('company_attendance_report_id', $eventData['user-variables'])) {
            if ($eventData['event'] === 'failed') {
                CompanyAttendanceReport::find($eventData['user-variables']['company_attendance_report_id'])
                    ->emails()
                    ->where('email', $eventData['recipient'])
                    ->update([
                        'failed_at' => now(),
                        'failed_reason' => $eventData['delivery-status']['message'],
                    ]);
            } elseif ($eventData['event'] === 'delivered') {
                CompanyAttendanceReport::find($eventData['user-variables']['company_attendance_report_id'])
                    ->emails()
                    ->where('email', $eventData['recipient'])
                    ->update([
                        'delivered_at' => now(),
                    ]);
            }
        }

        dd($request->get('event-data')['user-variables']);
        if ($request['user-variables']->has('company_attendance_report_id')) {
            Log::info('Caught!'. $request['user-variables']->get('company_attendance_report_id'));
            if ($request->event === 'failed') {
                CompanyAttendanceReport::find($request->message->headers->get('Ptc-Company-Attendance-Report-Id'))
                    ->emails()
                    ->where('email', $request->event->recipient)
                    ->update([
                        'failed_at' => now(),
                        'failed_reason' => $request->reason,
                    ]);
            } elseif ($request->event === 'delivered') {
                CompanyAttendanceReport::find($request->message->headers->get('Ptc-Company-Attendance-Report-Id'))
                    ->emails()
                    ->where('email', $request->event->recipient)
                    ->update([
                        'delivered_at' => now(),
                    ]);
            }
        }

        dd('done');

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
