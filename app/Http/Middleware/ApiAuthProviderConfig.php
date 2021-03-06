<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 14.06.2019
 * Time: 17:44
 */

namespace App\Http\Middleware;


use App\Models\Users\Auth\AuthProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiAuthProviderConfig {
    /**
     * @var AuthProvider
     */
    protected $authProviders;

    /**
     * ApiAuthProviderConfig constructor.
     * @param AuthProvider $authProviders
     */
    public function __construct(AuthProvider $authProviders) {
        $this->authProviders = $authProviders;
    }

    /**
     * @param Request  $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next) {
        config($this->authProviders::getConfigsForApi());

        return $next($request);
    }
}
