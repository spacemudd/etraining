<?php

namespace App\Http\Controllers;

use App\Services\ZoomService;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    protected $service;

    public function __construct(ZoomService $service)
    {
        $this->service = $service;
    }

    function signature(Request $request)
    {
        $request->validate([
            'meeting_id' => 'string',
            'role' => 'string',
        ]);

        return $this->service->generateSignature($request->meeting_id, $request->role);
    }
}
