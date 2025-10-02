<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearOldCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Aggressively clear ALL problematic cookies on every request
        // This ensures any old cookies are immediately removed
        $this->clearAllProblematicCookies($request, $response);
        
        return $response;
    }
    
    private function clearAllProblematicCookies(Request $request, $response)
    {
        // Get all cookies from the request
        $allCookies = $request->cookies->all();
        
        // Clear any cookie that matches problematic patterns
        foreach ($allCookies as $cookieName => $cookieValue) {
            if ($this->shouldClearCookie($cookieName, $cookieValue)) {
                $this->clearCookieAllVariations($response, $cookieName);
            }
        }
        
        // Also clear known problematic cookies
        $knownProblematicCookies = [
            'jasarah_session', // Old session cookie name
            'laravel_session',
            'laravel_session_v2',
            'WO2SItDCOibKE3wWKiuJXujUgLqgqpfK0XZOOhzB',
            '5W2zeCSMI1FjMxIR6Yrv292Ka3adj5jHnRigZobB',
            'H0XOP1OLhFFsr3fwuPMjRa6WI37FI7kRlwc48STw',
            'OE33EnfT05Anwq71y1S0WfSz2DcCV3uemLI8Rb4s',
            'YwHNhWTZuuhaTiGKZXQi6N8y1yqTWmiiF6Ha59dp'
        ];
        
        foreach ($knownProblematicCookies as $cookieName) {
            $this->clearCookieAllVariations($response, $cookieName);
        }
    }
    
    private function shouldClearCookie($cookieName, $cookieValue)
    {
        // Clear cookies that look like Laravel encrypted cookies (they start with eyJ)
        if (str_starts_with($cookieValue, 'eyJ')) {
            return true;
        }
        
        // Clear old session cookies
        if (in_array($cookieName, ['jasarah_session', 'laravel_session', 'laravel_session_v2'])) {
            return true;
        }
        
        // Clear any cookie that looks like a random hash (problematic encrypted cookies)
        if (strlen($cookieName) > 40 && preg_match('/^[A-Za-z0-9]+$/', $cookieName)) {
            return true;
        }
        
        return false;
    }
    
    private function clearCookieAllVariations($response, $cookieName)
    {
        $domains = [
            '.jasarah-ksa.com',
            'jasarah-ksa.com', 
            'app.jasarah-ksa.com',
            '.app.jasarah-ksa.com'
        ];
        
        $secureOptions = [true, false];
        $sameSiteOptions = ['Lax', 'Strict', 'None'];
        
        foreach ($domains as $domain) {
            foreach ($secureOptions as $secure) {
                foreach ($sameSiteOptions as $sameSite) {
                    // Clear with different expiration times to ensure it's gone
                    $response->headers->setCookie(
                        cookie($cookieName, '', -1, '/', $domain, $secure, true, false, $sameSite)
                    );
                    $response->headers->setCookie(
                        cookie($cookieName, '', -86400, '/', $domain, $secure, true, false, $sameSite) // 1 day ago
                    );
                    $response->headers->setCookie(
                        cookie($cookieName, '', -2592000, '/', $domain, $secure, true, false, $sameSite) // 30 days ago
                    );
                }
            }
        }
    }
}
