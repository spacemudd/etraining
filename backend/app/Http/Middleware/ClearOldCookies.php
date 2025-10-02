<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearOldCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only clear on successful responses
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            // Clear old session cookie
            $response->headers->setCookie(
                cookie('jasarah_session', '', -1, '/', '.jasarah-ksa.com', false, true, false, 'Lax')
            );
            
            // Clear old XSRF token
            $response->headers->setCookie(
                cookie('XSRF-TOKEN', '', -1, '/', '.jasarah-ksa.com', false, false, false, 'Lax')
            );
            
            // Clear any other old cookies you see
            $response->headers->setCookie(
                cookie('WO2SItDCOibKE3wWKiuJXujUgLqgqpfK0XZOOhzB', '', -1, '/', '.jasarah-ksa.com', false, true, false, 'Lax')
            );
        }
        
        return $response;
    }
}
