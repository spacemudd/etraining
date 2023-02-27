<?php

namespace App\Services;

use App\Models\Back\Company;

class CompaniesAssignedToRiyadhBank
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
}
