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
        // dd("here");
        $this->authorize('view-gosi');

        $request->validate([
            'ninOrIqama' => 'required|numeric|digits:10',
        ]);


        $cacheKey = 'gosi_' . $request->ninOrIqama;
        $counterKey = 'gosi_requests_counter';
        $lastResetKey = 'gosi_last_reset';

        //check the last date
        $lastReset = Cache::get($lastResetKey, null);

        //if counter and date not initialized or exceed the limit , initialize them for 30 days
        if (!$lastReset || now()->diffInDays($lastReset) >= 30) {
            Cache::put($counterKey, 0, self::CACHE_DURATION);
            Cache::put($lastResetKey, now(), self::CACHE_DURATION);
        }

        //check count of request , if not->return zero
        $requestCount = Cache::get($counterKey, 0);

        //check if the counter exceed the limit -> return error
        if ($requestCount >= self::MAX_REQUESTS) {
            return response()->json(['error' => 'Monthly request limit exceeded'], 429);
        }

        //check if data is already im cashe
        $data = Cache::get($cacheKey);

        if (!$data) {
            //if no data found for this cashekey -> get it and increment counter
            $data = GosiEmployee::new($request->ninOrIqama)->get()->toArray();

            Cache::put($cacheKey, $data, self::CACHE_DURATION);
            Cache::increment($counterKey);
        }

        return response()->json($data);
    }

    // public function show(Request $request)
    // {
    //     $this->authorize('view-gosi');

    //     $request->validate([
    //         'ninOrIqama' => 'required|numeric|digits:10',
    //     ]);

    //     return response()->json(GosiEmployee::new($request->ninOrIqama)->get()->toArray());
    // }
}
