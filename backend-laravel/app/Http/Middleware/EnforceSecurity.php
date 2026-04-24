<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceSecurity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('security.force_https') && ! $request->isSecure()) {
            return redirect()->to('https://'.$request->getHttpHost().$request->getRequestUri(), 301);
        }

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        $response->headers->set('Referrer-Policy', (string) config('security.referrer_policy', 'strict-origin-when-cross-origin'));
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', (string) config('security.x_frame_options', 'SAMEORIGIN'));
        $response->headers->set('Permissions-Policy', (string) config('security.permissions_policy', 'camera=(), geolocation=(), microphone=()'));
        $response->headers->set('Cross-Origin-Opener-Policy', (string) config('security.cross_origin_opener_policy', 'same-origin'));
        $response->headers->set('Cross-Origin-Resource-Policy', (string) config('security.cross_origin_resource_policy', 'same-origin'));

        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age='.(int) config('security.hsts_max_age', 31536000).'; includeSubDomains'
            );
        }

        return $response;
    }
}
