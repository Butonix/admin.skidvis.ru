<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class ForAdministrators {
    /**
     * @param         $request
     * @param Closure $next
     *
     * @return mixed
     *
     * @since version
     */
    public function handle($request, Closure $next) {
        $developersIds = [
            1,
        ];

        if (auth()->check() && \in_array(auth()->id(), $developersIds, true)) {
            config([
                'app.debug'        => true,
                'debugbar.enabled' => true,
            ]);
        } else {
            config([
                'app.debug'        => false,
                'debugbar.enabled' => false,
            ]);
        }

        return $next($request);
    }
}
