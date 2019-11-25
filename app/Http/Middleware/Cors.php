<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors {
    private static $allowedOriginsWhitelist = [
        //'http://localhost:80'
        'http://5.45.80.118:3000',
    ];

    // All the headers must be a string

    private static $allowedOrigin = '*';

    private static $allowedMethods = 'OPTIONS, GET, POST, PUT, PATCH, DELETE';

    private static $allowCredentials = 'true';

    private static $allowedHeaders = '';

    private static $allowedMaxAge = 3600;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!$this->isCorsRequest($request)) {
            return $next($request);
        }

        static::$allowedOrigin = $this->resolveAllowedOrigin($request);
        static::$allowedHeaders = $this->resolveAllowedHeaders($request);

        $headers = [
            'Access-Control-Allow-Origin'      => static::$allowedOrigin,
            'Access-Control-Allow-Methods'     => static::$allowedMethods,
            'Access-Control-Allow-Headers'     => static::$allowedHeaders,
            'Access-Control-Allow-Credentials' => static::$allowCredentials,
            'Access-Control-Max-Age'           => static::$allowedMaxAge,
        ];

        // For preflighted requests
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)->withHeaders($headers);
        }

        $response = $next($request)->withHeaders($headers);

        return $response;

        //return $next($request);
    }

    /**
     * Incoming request is a CORS request if the Origin
     * header is set and Origin !== Host
     *
     * @param Request $request
     *
     * @return bool
     */
    private function isCorsRequest(Request $request): bool {
        $requestHasOrigin = $request->headers->has('Origin');

        if ($requestHasOrigin) {
            $origin = $request->headers->get('Origin');

            $host = $request->getSchemeAndHttpHost();

            if ($origin !== $host) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dynamic resolution of allowed origin since we can't
     * pass multiple domains to the header. The appropriate
     * domain is set in the Access-Control-Allow-Origin header
     * only if it is present in the whitelist.
     *
     * @param Request $request
     *
     * @return null|string|string[]
     */
    private function resolveAllowedOrigin(Request $request) {
        $allowedOrigin = static::$allowedOrigin;

        // If origin is in our $allowedOriginsWhitelist
        // then we send that in Access-Control-Allow-Origin

        $origin = $request->headers->get('Origin');

        if (in_array($origin, static::$allowedOriginsWhitelist)) {
            $allowedOrigin = $origin;
        }

        return $allowedOrigin;
    }

    /**
     *
     * Take the incoming client request headers
     * and return. Will be used to pass in Access-Control-Allow-Headers
     *
     * @param Request $request
     *
     * @return null|string|string[]
     */
    private function resolveAllowedHeaders(Request $request) {
        $allowedHeaders = $request->headers->get('Access-Control-Request-Headers');

        return $allowedHeaders;
    }
}
