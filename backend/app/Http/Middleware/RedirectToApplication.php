<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToApplication
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
        if (auth()->user()->instructor) {
            if (auth() && optional(auth()->user())->instructor && ! optional(auth()->user()->instructor)->is_approved) {
                return redirect()->route('register.instructors.application');
            }
        }

        if (auth()->user()->trainee) {
            if (auth() && optional(auth()->user())->trainee && ! optional(auth()->user()->trainee)->is_approved) {
                return redirect()->route('register.trainees.application');
            }
        }

        return $next($request);
    }
}
