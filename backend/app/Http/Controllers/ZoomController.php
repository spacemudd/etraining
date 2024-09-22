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

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function signature(Request $request): string
    {
        $request->validate([
            'meeting_id' => 'numeric',
            'role' => 'numeric',
        ]);

        return $this->service->generateSignature($request->meeting_id, $request->role);
    }
}
