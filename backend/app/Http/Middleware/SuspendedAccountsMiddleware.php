<?php

namespace App\Http\Middleware;

use App\Models\Back\TraineeBlockList;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SuspendedAccountsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // NOTE: This was disabled in delivering D-PTC-12.
        // The email should be titled 'D-PTC-12'.

        // This is enabled after 12-04-2023 - Shafiq.

        if ($trainee = optional(auth()->user())->trainee) {
            if ($trainee->deleted_at && !$trainee->has_outstanding_amount) {
                return Inertia::render('Trainees/Dashboard');
            } else if ($trainee->deleted_at && $trainee->has_outstanding_amount) {
                return Inertia::render('Trainees/Payment/IndexTap');
            }

            $suspended = TraineeBlockList::where('name', $trainee->name)
                ->orWhere('phone', $trainee->phone)
                // ->orWhere('phone_additional', $trainee->phone_additional)
                ->orWhere('email', $trainee->email)
                ->orWhere('identity_number', $trainee->identity_number)
                ->exists();

            if ($suspended) {
                abort('412', 'Account disabled');
            }
        }
        return $next($request);
    }
}
