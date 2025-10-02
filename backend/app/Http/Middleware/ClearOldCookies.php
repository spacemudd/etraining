<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearOldCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only clear old cookies if we're not on a login/verification page
        // This prevents interfering with session creation during login
        if (!str_contains($request->path(), 'login') && !str_contains($request->path(), 'verify')) {
            // Clear old session cookies with different domain variations
            // This is now more targeted - only clear cookies that definitely shouldn't exist
            $oldCookies = [
                'jasarah_session', // Old session cookie name
                'WO2SItDCOibKE3wWKiuJXujUgLqgqpfK0XZOOhzB', // Specific old cookie from DevTools
                '5W2zeCSMI1FjMxIR6Yrv292Ka3adj5jHnRigZobB', // From DevTools screenshot
                'H0XOP1OLhFFsr3fwuPMjRa6WI37FI7kRlwc48STw', // From DevTools screenshot
                'OE33EnfT05Anwq71y1S0WfSz2DcCV3uemLI8Rb4s', // From DevTools screenshot
                'YwHNhWTZuuhaTiGKZXQi6N8y1yqTWmiiF6Ha59dp'  // From DevTools screenshot
            ];
            
            $domains = ['.jasarah-ksa.com', 'jasarah-ksa.com', 'app.jasarah-ksa.com'];
            
            foreach ($oldCookies as $cookieName) {
                foreach ($domains as $domain) {
                    // Clear with both secure and non-secure options
                    $response->headers->setCookie(
                        cookie($cookieName, '', -1, '/', $domain, false, true, false, 'Lax')
                    );
                    $response->headers->setCookie(
                        cookie($cookieName, '', -1, '/', $domain, true, true, false, 'Lax')
                    );
                }
            }
        }
        
        return $response;
    }
}
