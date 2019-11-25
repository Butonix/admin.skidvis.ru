<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\Login;
use App\Http\Controllers\Controller;
use App\Models\Users\Auth\AuthProvider;
use App\Models\Users\Auth\UserAccount;
use App\Models\Users\User;
use App\Notifications\NewUser;
use Artesaos\SEOTools\Facades\SEOMeta;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteManager;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * @var AuthProvider
     */
    protected $providers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @param AuthProvider $providers
     *
     * @return void
     */
    public function __construct(AuthProvider $providers) {
        $this->providers = $providers;
        $this->middleware('guest')->except('logout');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function attemptLogin(Request $request) {
        $token = Auth::guard('api')->attempt($this->credentials($request));

        if ($token) {
            Auth::guard('api')->setToken($token);

            return true;
        }

        return false;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function sendLoginResponse(Request $request) {
        $this->clearLoginAttempts($request);

        $token = (string)Auth::guard('api')->getToken();
        $expiration = Auth::guard('api')->getPayload()->get('exp');
        event(new Login(Auth::guard('api'), Auth::guard('api')->user(), $request->filled('remember')));

        return [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => 3600 * 24 * 365,
        ];
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        Auth::guard('api')->logout();
    }

    /**
     * @param Request $request
     * @param         $provider
     *
     * @return mixed
     */
    public function redirectToProvider(Request $request, $provider) {
        //return Socialite::driver($provider)->stateless()->redirect();
        return response()->json([
            'url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ], 200);
    }

    /**
     * @param User $user
     */
    protected function notificationRecentlyCreatedUsers(User $user): void {
        if (true === $user->wasRecentlyCreated) {
            $user->notify(new NewUser());
        }
    }

    /**
     * @param $provider
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function handleProviderCallback($provider) {
        /**
         * @var SocialiteManager $providerUser
         * @var UserAccount      $account
         */
        $providerUser = Socialite::driver($provider)->stateless()->user();
        $authProvider = AuthProvider::where('slug', $provider)->first();
        switch ($provider) {
            case 'facebook':
                {
                    $email = mb_strtolower(trim($providerUser->email), 'UTF-8');

                    if ($email !== '') {
                        /**
                         * @var User $user
                         */
                        $user = User::firstOrCreate(['email' => $email]);
                        $user->setFullNameSafely($providerUser->name);
                        $user->save();

                        if ($authProvider !== null) {
                            $account = $user->accounts()->firstOrCreate([
                                'auth_provider_id' => $authProvider->getKey(),
                            ]);
                            $account->setProviderUserId((int)$providerUser->id);
                            $account->payload = $providerUser;
                            $account->save();
                        }
                    }
                }
                break;
            case 'google':
                {
                    $email = mb_strtolower(trim($providerUser->email), 'UTF-8');
                    if ($email !== '') {
                        /**
                         * @var User $user
                         */
                        $user = User::firstOrCreate(['email' => $email]);
                        $user->setFullNameSafely($providerUser->name);
                        $user->save();

                        if ($authProvider !== null) {
                            $account = $user->accounts()->firstOrCreate([
                                'auth_provider_id' => $authProvider->getKey(),
                            ]);
                            $account->setProviderUserId((int)$providerUser->id);
                            $account->payload = $providerUser;
                            $account->save();
                        }
                    }
                }
                break;
            case 'vkontakte':
                {
                    $email = mb_strtolower(trim($providerUser->accessTokenResponseBody['email']), 'UTF-8');
                    if ($email !== '') {
                        /**
                         * @var User $user
                         */
                        $user = User::firstOrCreate(['email' => $email]);
                        $user->setFullNameSafely($providerUser->name);
                        $user->save();

                        if ($authProvider !== null) {
                            $account = $user->accounts()->firstOrCreate([
                                'auth_provider_id' => $authProvider->getKey(),
                            ]);
                            $account->setProviderUserId((int)$providerUser->id);
                            $account->payload = $providerUser;
                            $account->save();
                        }
                    }
                }
                break;
        }

        if (isset($user)) {
            Auth::guard('api')->login($user, true);
            $this->notificationRecentlyCreatedUsers($user);
            $token = JWTAuth::fromUser($user);
            event(new Login(Auth::guard('api'), $user, true));

            return response()->view('oauth.callback', [
                'token'      => $token,
                'token_type' => 'bearer',
                'expires_in' => 3600 * 24 * 365,
                'url'        => env('APP_BASE_URL'),
                'provider'   => $provider,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'alert'  => [
                    'type' => 'error',
                    'text' => 'Авторизация не удалась.',
                ],
                'action' => [
                    'type' => null,
                    'url'  => null,
                ],
            ], 401);
        }
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request) {
        $this->validate($request, [
            $this->username()      => 'required|string',
            'password'             => 'required|string',
            //			'g-recaptcha-response' => 'required|captcha', // depricated for CN users -> security bag
            'g-recaptcha-response' => 'captcha',
        ]);
    }

    /**
     * @return mixed
     */
    public function refresh() {
        $token = JWTAuth::getToken();
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'alert' => [
                    'type' => 'error',
                    'text' => 'Токен не предоставлен.'
                ],
                'action' => [
                    'type' => null,
                    'url' => null
                ]
            ], 401);
        }
        try {
            $token = JWTAuth::refresh($token);
        } catch (TokenInvalidException $exception) {
            return response()->json([
                'status' => 'error',
                'alert' => [
                    'type' => 'error',
                    'text' => 'Токен недействителен.'
                ],
                'action' => [
                    'type' => null,
                    'url' => null
                ]
            ], 422);
        }
        return response()->json([
            'token' => $token
        ], 200);
    }
}
