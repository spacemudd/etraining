<?php

namespace App\Services;

use App\Models\Back\Company;
use Illuminate\Mail\MailServiceProvider;

class CompanyMigrationHelper
{
    /**
     * @param string $company_id
     * @return void
     */
    public function setTapKey(string $company_id)
    {
        if (Company::withTrashed()->find($company_id)->is_ptc_net) {
            config(['tap-payment.auth.api_key' => env('TAP_PAYMENT_API_KEY_SECONDARY')]);
        }
    }

    public function setSecondaryTap()
    {
        config(['tap-payment.auth.api_key' => env('TAP_PAYMENT_API_KEY_SECONDARY')]);
    }

    public function setMailgunConfig()
    {
        config([
            'mail.mailers.from.address' => 'noreply@comms.ptc-ksa.net',
            'mail.mailers.from.name' => 'PTC-KSA.NET',
            'mail.mailers.mailgun.domain' => env('MAILGUN_DOMAIN_PTC_NET'),
            'mail.mailers.mailgun.secret' => env('MAILGUN_SECRET_PTC_NET'),
            'mail.mailers.mailgun.endpoint' => 'api.mailgun.net',
        ]);
        (new MailServiceProvider(app()))->register();
    }

    public static function setMailgunConfigStatic()
    {
        config([
            'mail.from.address' => 'noreply@comms.ptc-ksa.net',
            'mail.from.name' => config('mail.from.name'),
            'mail.mailers.mailgun.domain' => env('MAILGUN_DOMAIN_PTC_NET'),
            'mail.mailers.mailgun.secret' => env('MAILGUN_SECRET_PTC_NET'),
            'mail.mailers.mailgun.endpoint' => 'api.mailgun.net',
        ]);
        (new MailServiceProvider(app()))->register();
    }

    static public function setMailgunConfigBasedOnDomain($domain)
    {
        // Unify the sending.
        config([
            'mail.from.address' => 'noreply@mg.noreplycenter.com',
            'mail.from.name' => 'PTC-KSA.NET',
            'mail.mailers.mailgun.domain' => env('MAILGUN_DOMAIN'),
            'mail.mailers.mailgun.secret' => env('MAILGUN_SECRET'),
            'mail.mailers.mailgun.endpoint' => 'api.mailgun.net',
        ]);
        (new MailServiceProvider(app()))->register();
        return 1;

        if ($domain === 'ptc-ksa.net') {
            config([
                'mail.from.address' => 'noreply@comms.ptc-ksa.net',
                'mail.from.name' => 'PTC-KSA.NET',
                'mail.mailers.mailgun.domain' => env('MAILGUN_DOMAIN_PTC_NET'),
                'mail.mailers.mailgun.secret' => env('MAILGUN_SECRET_PTC_NET'),
                'mail.mailers.mailgun.endpoint' => 'api.mailgun.net',
            ]);
            (new MailServiceProvider(app()))->register();
        }

        if ($domain === 'jisr-ksa.com') {
            config([
                'mail.from.address' => 'noreply@jisr-ksa.com',
                'mail.from.name' => 'Jisr KSA',
                'mail.mailers.mailgun.domain' => env('MAILGUN_DOMAIN_JISR_DOMAIN'),
                'mail.mailers.mailgun.secret' => env('MAILGUN_SECRET_JISR_DOMAIN'),
                'mail.mailers.mailgun.endpoint' => 'api.mailgun.net',
            ]);
            (new MailServiceProvider(app()))->register();

        }

        if ($domain === 'jasarah-ksa.com') {
            config([
                'mail.from.address' => 'noreply@jasarah-ksa.com',
                'mail.from.name' => 'Jasarah KSA',
                'mail.mailers.mailgun.domain' => env('MAILGUN_DOMAIN_JASARAH_DOMAIN'),
                'mail.mailers.mailgun.secret' => env('MAILGUN_SECRET_JASARAH_DOMAIN'),
                'mail.mailers.mailgun.endpoint' => 'api.mailgun.net',
            ]);
            (new MailServiceProvider(app()))->register();
        }

        return null;
    }
}
