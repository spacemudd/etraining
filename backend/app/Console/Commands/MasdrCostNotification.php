<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RequestCounter;
use App\Models\AppSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class MasdrCostNotification extends Command
{
    protected $signature = 'masdr:gosi-usage-report';
    protected $description = 'Report on GOSI API usage and cost analytics';

    public function handle()
    {
        $now = Carbon::now();

        $currentMonth = $now->format('Y-m');

        $currentWeekStart = $now->copy()->startOfWeek();
        $currentWeekEnd = $now->copy()->endOfWeek();

        $previousWeekStart = $now->copy()->subWeek()->startOfWeek();
        $previousWeekEnd = $now->copy()->subWeek()->endOfWeek();

        $currentMonthCounter = RequestCounter::where('month', $currentMonth)->first();

        $currentWeekRequests = \App\Models\GosiEmployeeData::whereBetween('updated_at', [$currentWeekStart, $currentWeekEnd])->count();
        $previousWeekRequests = \App\Models\GosiEmployeeData::whereBetween('updated_at', [$previousWeekStart, $previousWeekEnd])->count();

        $maxMonthlyLimit = (int) (AppSetting::where('name', 'gosi_monthly_requests')->value('value') ?? 600);
        $requestsUsed = $currentMonthCounter ? $currentMonthCounter->count : 0;
        $requestsRemaining = $maxMonthlyLimit - $requestsUsed;

        $costSoFar = $requestsUsed * 7.36;

        $costSoFarFormatted = number_format($costSoFar, 2);

        $weekNumber = (int) ceil($now->day / 7);

        Mail::send('emails.gosi_report', [
            'currentMonth' => $currentMonth,
            'requestsUsed' => $requestsUsed,
            'costSoFarFormatted' => $costSoFarFormatted,
            'requestsRemaining' => $requestsRemaining,
            'currentWeekRequests' => $currentWeekRequests,
            'previousWeekRequests' => $previousWeekRequests,
        ], function ($mail) use ($weekNumber, $currentMonth) {
            $mail->to('cfo@hadaf-hq.com')
                 ->bcc('shafiqalshaar@adv-line.com')
                 ->subject("📊 تقرير مصدر - {$currentMonth} - الأسبوع رقم {$weekNumber}");
        });

        return 1;
    }
}
