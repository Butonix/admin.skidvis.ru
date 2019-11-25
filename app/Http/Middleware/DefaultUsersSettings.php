<?php

namespace App\Http\Middleware;

use App\Models\Cities\City;
use App\Models\Users\User;
use Closure;

class DefaultUsersSettings {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        //\Log::debug('asdasdasdasd: ', (array)$request->cookies);
        //
        //if (\Auth::guard('api')->check()) {
        //    /**
        //     * @var User $user
        //     */
        //    $user = \Auth::guard('api')->user();
        //    if (is_null($user->getCity())) {
        //        $user->city()->associate(City::DEFAULT_TIMEZONE_FOR_USERS);
        //        $user->save();
        //    }
        //} else {
        //    if (!$request->session()->has(User::SESSION_KEY_FOR_CITY_ID)) {
        //        $request->session()->put(User::SESSION_KEY_FOR_CITY_ID, City::DEFAULT_TIMEZONE_FOR_USERS);
        //        $request->session()->put(User::SESSION_KEY_FOR_CITY_NAME, City::DEFAULT_CITY_FOR_USERS);
        //    }
        //}
        //
        return $next($request);
    }
}
