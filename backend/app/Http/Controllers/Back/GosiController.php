<?php

namespace App\Http\Controllers\Back;

use App\Classes\GosiEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class GosiController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException|\Illuminate\Auth\Access\AuthorizationException
     */

    const CACHE_DURATION = 60 * 24 * 30;
    const MAX_REQUESTS = 1500;   //8.60 SR per request
    public function show(Request $request)
    {
        $this->authorize('view-gosi');

        $request->validate([
            'ninOrIqama' => 'required|numeric|digits:10',
        ]);

        $forceFresh = $request->boolean('force');
        $data = GosiEmployee::new($request->ninOrIqama, $request->only([
            'reason_employment_office',
            'reason_collection',
            'reason_trainee_affairs',
            'reason_sales',
            'reason_other',
        ]))->get($forceFresh)->toArray();

        return response()->json($data);
    }
}
