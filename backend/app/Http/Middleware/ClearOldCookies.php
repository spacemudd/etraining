<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearOldCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only clear old cookies, don't interfere with session creation
        // Clear old session cookie with different domain variations
        $response->headers->setCookie(
            cookie('jasarah_session', '', -1, '/', '.jasarah-ksa.com', false, true, false, 'Lax')
        );
        
        $response->headers->setCookie(
            cookie('jasarah_session', '', -1, '/', 'jasarah-ksa.com', false, true, false, 'Lax')
        );
        
        // Clear old XSRF token
        $response->headers->setCookie(
            cookie('XSRF-TOKEN', '', -1, '/', '.jasarah-ksa.com', false, false, false, 'Lax')
        );
        
        $response->headers->setCookie(
            cookie('XSRF-TOKEN', '', -1, '/', 'jasarah-ksa.com', false, false, false, 'Lax')
        );
        
        // Clear any other old cookies
        $response->headers->setCookie(
            cookie('WO2SItDCOibKE3wWKiuJXujUgLqgqpfK0XZOOhzB', '', -1, '/', '.jasarah-ksa.com', false, true, false, 'Lax')
        );
        
        $response->headers->setCookie(
            cookie('WO2SItDCOibKE3wWKiuJXujUgLqgqpfK0XZOOhzB', '', -1, '/', 'jasarah-ksa.com', false, true, false, 'Lax')
        );
        
        return $response;
    }
}
