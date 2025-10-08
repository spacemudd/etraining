<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class FixSessionCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only apply fixes for the main application domain
        if (str_contains($request->getHost(), 'jasarah-ksa.com')) {
            // First, aggressively clear all problematic cookies
            $this->clearProblematicCookies($request, $response);
            
            // Then set the correct cookies
            $this->fixSessionCookie($request, $response);
            $this->fixXSRFToken($request, $response);
        }
        
        return $response;
    }
    
    private function clearProblematicCookies(Request $request, $response)
    {
        // Get all cookies from the request
        $allCookies = $request->cookies->all();
        
        // Clear any cookie that matches problematic patterns
        foreach ($allCookies as $cookieName => $cookieValue) {
            // Clear cookies with wrong domains or old cookies
            if ($this->shouldClearCookie($cookieName, $cookieValue)) {
                $this->clearCookieAllVariations($response, $cookieName);
            }
        }
        
        // Also clear known problematic cookies from your DevTools screenshot
        $knownProblematicCookies = [
            '5W2zeCSMI1FjMxIR6Yrv292Ka3adj5jHnRigZobB',
            'H0XOP1OLhFFsr3fwuPMjRa6WI37FI7kRlwc48STw',
            'OE33EnfT05Anwq71y1S0WfSz2DcCV3uemLI8Rb4s',
            'YwHNhWTZuuhaTiGKZXQi6N8y1yqTWmiiF6Ha59dp',
            'WO2SItDCOibKE3wWKiuJXujUgLqgqpfK0XZOOhzB',
            'jasarah_session', // Old session cookie
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
    
    private function fixSessionCookie(Request $request, $response)
    {
        $sessionName = config('session.cookie', 'jasarah_session_v2');
        $sessionId = session()->getId();
        
        // If we have a valid session ID, ensure the cookie is set correctly
        if ($sessionId && session()->isStarted()) {
            // Remove old session cookies with wrong domains/security settings
            $this->removeOldCookies($response, $sessionName);
            
            // Set the correct session cookie
            $response->headers->setCookie(
                cookie(
                    $sessionName,
                    $sessionId,
                    config('session.lifetime', 480) * 60, // Convert to seconds
                    '/',
                    '.jasarah-ksa.com', // Use wildcard domain
                    config('session.secure', false), // Allow HTTP and HTTPS
                    true, // HttpOnly
                    false, // Raw
                    'Lax' // SameSite
                )
            );
        }
    }
    
    private function fixXSRFToken(Request $request, $response)
    {
        $token = csrf_token();
        
        if ($token) {
            // Remove old XSRF cookies with wrong domains/security settings
            $this->removeOldCookies($response, 'XSRF-TOKEN');
            
            // Set the correct XSRF cookie
            $response->headers->setCookie(
                cookie(
                    'XSRF-TOKEN',
                    $token,
                    0, // Session cookie
                    '/',
                    '.jasarah-ksa.com', // Use wildcard domain
                    config('session.secure', false), // Allow HTTP and HTTPS
                    false, // Not HttpOnly (needed for JS access)
                    false, // Raw
                    'Lax' // SameSite
                )
            );
        }
    }
    
    private function removeOldCookies($response, $cookieName)
    {
        // Remove cookies with various domain configurations that might cause conflicts
        $domains = [
            'jasarah-ksa.com',
            'app.jasarah-ksa.com',
            '.app.jasarah-ksa.com'
        ];
        
        $secureOptions = [true, false];
        
        foreach ($domains as $domain) {
            foreach ($secureOptions as $secure) {
                $response->headers->setCookie(
                    cookie($cookieName, '', -1, '/', $domain, $secure, true, false, 'Lax')
                );
            }
        }
    }
}
