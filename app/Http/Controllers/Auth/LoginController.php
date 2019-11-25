<?php

namespace App\Http\Controllers\Auth;

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
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteManager;

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm() {
        SEOMeta::setTitle('Вход');
        $providers = $this->providers::published()->get();

        return view('auth.login', compact('providers'));
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request) {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath())->with('flash_message', [
                'type' => 'success',
                'text' => 'Вы успешно авторизовались',
            ]);
    }

    /**
     * @param Request $request
     * @param         $provider
     *
     * @return mixed
     */
    public function redirectToProvider(Request $request, $provider) {
        return Socialite::driver($provider)->redirect();
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
     * @param           $provider
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallback($provider) {
        /**
         * @var SocialiteManager $providerUser
         * @var UserAccount      $account
         */
        $providerUser = Socialite::driver($provider)->user();
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
            Auth::login($user, true);
            $this->notificationRecentlyCreatedUsers($user);
        }

        return redirect($this->redirectTo)->with('flash_message', [
            'type' => 'success',
            'text' => 'Вы успешно авторизовались',
        ]);
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
}
