<?php

namespace App\Services;

use App\Models\Back\Company;

class CompanyMigrationHelper
{
    /**
     * @param string $company_id
     * @return void
     */
    public function setTapKey(string $company_id)
    {
        if (Company::find($company_id)->is_ptc_net) {
            config(['tap-payment.auth.api_key' => env('TAP_PAYMENT_API_KEY_SECONDARY')]);
        }
    }

    public function setSecondaryTap()
    {
        config(['tap-payment.auth.api_key' => env('TAP_PAYMENT_API_KEY_SECONDARY')]);
    }

    public function setMailgunConfig()
    {
        config(['mail.mailers.from.address' => 'noreply@ptc-ksa.net']);
        config(['mail.mailers.from.name' => 'PTC-KSA.NET']);
        config(['mail.mailers.mailgun.domain' => env('MAILGUN_DOMAIN_PTC_NET')]);
        config(['mail.mailers.mailgun.secret' => env('MAILGUN_SECRET_PTC_NET')]);
        config(['mail.mailers.mailgun.endpoint' => 'api.mailgun.net']);
    }
}
