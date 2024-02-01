<?php

namespace App\Services;

use App\Mail\DeleteCompanyEmail;
use App\Mail\NewCompanyEmail;
use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;
use App\Models\User;
use App\Notifications\DeleteCompanyNotification;
use App\Notifications\NewCompanyNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class CompaniesService
{
    /**
     * @return \App\Services\CompaniesService
     */
    public static function new(): CompaniesService
    {
        return new self;
    }

    /**
     * Find a company by its domain name.
     *
     * @param $domain
     * @return \App\Models\Back\Company|null
     */
    public function findByDomainName($domain)
    {
        $company = Company::where('email', 'LIKE', '%'.$domain.'%')->first();

        if ($company) {
            return $company;
        }

        $report = CompanyAttendanceReport::where('to_emails', 'LIKE', '%'.$domain.'%')->first();

        return optional($report)->company;
    }

    public function notifyUsersAboutNewCompany(Company $company)
    {
        // TODO: Instead of getting users, get all users with specific permission and send them.
        $users = [
            'ceo@ptc-ksa.net',
            'sara@ptc-ksa.net',
            'cfo@ptc-ksa.net',
            'acc@ptc-ksa.net',
            'm_shehatah@ptc-ksa.net',
            'eslam@ptc-ksa.net',
            'collectionm@ptc-ksa.net',
            'mashael.a@ptc-ksa.net',
        ];
        Mail::to($users)->send(new NewCompanyEmail($company->id));
    }

    public function notifyUsersAboutDeletedCompany(Company $company)
    {
        // TODO: Instead of getting users, get all users with specific permission and send them.
        $users = [
            'ceo@ptc-ksa.net',
            'sara@ptc-ksa.net',
            'cfo@ptc-ksa.net',
            'acc@ptc-ksa.net',
            'm_shehatah@ptc-ksa.net',
            'eslam@ptc-ksa.net',
            'collectionm@ptc-ksa.net',
            'mashael.a@ptc-ksa.net',
        ];
        Mail::to($users)->send(new DeleteCompanyEmail($company->id));
    }
}
