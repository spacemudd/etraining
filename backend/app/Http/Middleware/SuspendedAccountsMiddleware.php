<?php

namespace App\Http\Middleware;

use App\Models\Back\TraineeBlockList;
use Closure;
use Illuminate\Http\Request;

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
        if ($trainee = optional(auth()->user())->trainee) {
            $suspended = TraineeBlockList::where('name', $trainee->name)
                ->orWhere('phone', $trainee->phone)
                ->orWhere('phone_additional', $trainee->phone_additional)
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
