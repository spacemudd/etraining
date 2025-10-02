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
            $this->fixSessionCookie($request, $response);
            $this->fixXSRFToken($request, $response);
        }
        
        return $response;
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
                    false, // Not secure for HTTP compatibility
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
                    false, // Not secure for HTTP compatibility
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
