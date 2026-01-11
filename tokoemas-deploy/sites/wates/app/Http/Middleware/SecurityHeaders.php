<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Content Security Policy (CSP)
        $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data: https://fonts.gstatic.com; connect-src 'self';";
        $response->headers->set('Content-Security-Policy', $csp, false);

        // X-Frame-Options (Anti-Clickjacking)
        $response->headers->set('X-Frame-Options', 'DENY', false);

        // X-Content-Type-Options (Anti-MIME Sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);

        // X-XSS-Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block', false);

        // Strict-Transport-Security (HSTS)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload', false);
        }

        return $response;
    }
}