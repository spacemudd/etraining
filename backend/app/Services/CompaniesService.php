<?php

namespace App\Services;

use App\Models\Back\Company;
use App\Models\Back\CompanyAttendanceReport;

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
     * @return \App\Models\Back\Company
     */
    public function findByDomainName($domain): Company
    {
        $company = Company::where('email', 'LIKE', '%'.$domain.'%')->first();

        if ($company) {
            return $company;
        }

        $report = CompanyAttendanceReport::where('to_emails', 'LIKE', '%'.$domain.'%')->first();

        return $report->company;
    }
}
