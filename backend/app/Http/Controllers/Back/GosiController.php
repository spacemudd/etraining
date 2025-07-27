<?php

namespace App\Http\Controllers\Back;

use App\Classes\GosiEmployee;
use App\Http\Controllers\Controller;
use App\Models\RequestCounter;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


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

    /**
     * Get 12-month GOSI request history
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMonthlyHistory()
    {
        $this->authorize('view-gosi');

        $monthlyHistory = [];
        $currentDate = Carbon::now();
        $monthlyLimit = (int) (AppSetting::where('name', 'gosi_monthly_requests')->value('value') ?? 600);

        // Get the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = $currentDate->copy()->subMonths($i);
            $monthKey = $date->format('Y-m');
            
            // Get request counter for this month
            $requestCounter = RequestCounter::where('month', $monthKey)->first();
            $requestsCount = $requestCounter ? $requestCounter->count : 0;
            
            // Check if this is the current month
            $isCurrentMonth = $i === 0;
            
            $monthlyHistory[] = [
                'monthName' => $date->locale('ar')->isoFormat('MMMM YYYY'),
                'requestsCount' => $requestsCount,
                'monthlyLimit' => $monthlyLimit,
                'isCurrentMonth' => $isCurrentMonth,
                'monthKey' => $monthKey,
                'totalCost' => $requestsCount * 7.36,
            ];
        }

        return response()->json([
            'monthlyHistory' => $monthlyHistory,
            'totalRequests' => collect($monthlyHistory)->sum('requestsCount'),
            'totalLimit' => collect($monthlyHistory)->sum('monthlyLimit'),
            'totalCost' => collect($monthlyHistory)->sum('totalCost'),
        ]);
    }
}
