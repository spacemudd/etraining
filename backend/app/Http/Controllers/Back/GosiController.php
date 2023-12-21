<?php

namespace App\Http\Controllers\Back;

use App\Classes\GosiEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GosiController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException|\Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Request $request)
    {
        $this->authorize('view-gosi');

        $request->validate([
            'ninOrIqama' => 'required|numeric|digits:10',
        ]);

        return response()->json(GosiEmployee::new($request->ninOrIqama)->get()->toArray());
    }
}
