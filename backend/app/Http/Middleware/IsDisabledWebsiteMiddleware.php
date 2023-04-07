<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class IsDisabledWebsiteMiddleware
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
        if (optional(auth()->user())->trainee || optional(auth()->user())->instructor) {
            if (auth()->user()->team->website_disabled) {
                if (!\Route::is('disabled')) {
                    return redirect()->route('disabled');
                }
            }
        }

        if (!str_contains(URL::current(), 'tap')) {
            return redirect()->route('closed');
        }

        return $next($request);
    }
}
