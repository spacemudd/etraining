<?php

namespace App\Classes;

use App\Models\Back\Audit;
use App\Models\Back\Trainee;
use Illuminate\Support\Facades\Http;

class ElmYakeen
{
    public $http_headers;

    public function __construct()
    {
        $this->http_headers = [
            'APP-ID' => config('yakeen.app_id'),
            'APP-KEY' => config('yakeen.app_key'),
            'SERVICE_KEY' => config('yakeen.service_key'),
            'ORGANIZATION-NUMBER' => config('yakeen.org_key'),
        ];
    }

    public static function new()
    {
        return new ElmYakeen();
    }

    /**
     * Check if phone number is owned under the identity number.
     *
     * @param string $id_number The identity number
     * @param string $phone_number Phone number to verify
     * @return void
     */
    public function verifyOwnership(string $id_number, string $phone_number)
    {
        return Http::withHeaders($this->http_headers)
            ->get('https://yakeen-lite.api.elm.sa:443/api/v1/person/'.$id_number.'/owns-mobile/'.$phone_number)
            ->json();
    }
}
