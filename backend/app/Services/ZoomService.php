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

use App\Models\Back\CourseBatchSession;
use MacsiDigital\Zoom\Support\Entry;
use MacsiDigital\Zoom\User;

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
     * Used for when joining the meeting via the browser.
     *
     * @param $meeting_number
     * @param $role
     * @return string
     */
    function generateSignature($meeting_number, $role)
    {
        $session = CourseBatchSession::where('zoom_meeting_id', $meeting_number)->first();

        $zoomSettings = $session->course->instructor->zoom_account;
        $zoom = new Entry($zoomSettings->account_id, $zoomSettings->client_id, $zoomSettings->client_secret);
        $user = new User($zoom);

        return '';

        // removed because zoom switched from jwt to oauth2.

        //$this->api_key = $zoomSettings->ZOOM_CLIENT_KEY;
        //$this->api_secret = $zoomSettings->ZOOM_CLIENT_SECRET;
        //
        //$time = time() * 1000 - 30000; // time in milliseconds (or close enough)
        //
        //$data = base64_encode($this->api_key . $meeting_number . $time . $role);
        //
        //$hash = hash_hmac('sha256', $data, $this->api_secret, true);
        //
        //$_sig = $this->api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);
        //
        //// return signature, url safe base64 encoded
        //return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }
}
