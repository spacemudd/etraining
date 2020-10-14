<?php
/**
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Services;

class ZoomService
{
    private $api_key;
    private $api_secret;
    private $meeting_number;
    private $role;

    public function __construct()
    {
        $this->api_key = config('zoom.api_key');
        $this->api_secret = config('zoom.api_secret');
    }

    /**
     *
     * @param $meeting_number
     * @param $role
     * @return string
     */
    function generateSignature($meeting_number, $role)
    {
        $time = time() * 1000 - 30000; // time in milliseconds (or close enough)

        $data = base64_encode($this->api_key . $meeting_number . $time . $role);

        $hash = hash_hmac('sha256', $data, $this->api_secret, true);

        $_sig = $this->api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);

        // return signature, url safe base64 encoded
        return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }
}
