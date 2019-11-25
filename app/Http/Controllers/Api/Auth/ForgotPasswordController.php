<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Users\User;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * @var User
     */
    protected $users;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(User $users) {
        $this->middleware('guest');
        $this->users = $users;
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request) {
        /**
         * @var User $user
         */
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $user = $this->users::where('email', $this->credentials($request))->first();
        $userForToken = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->getUser($this->credentials($request));

        if (is_null($user)) {
            return response()->json([
                'status'  => 'error',
                'alert'   => [
                    'type'  => 'error',
                    'text' => 'Пользователь с данным email не найден.'
                ],
                'actions' => [
                    'type' => null,
                    'url'  => null
                ],
            ], 400);
        }

        $token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($userForToken);
        $user->sendPasswordResetNotification($token, true, $request->headers->get('origin'));

        return $this->sendResetLinkResponse($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request) {
        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Ссылка на сброс пароля успешно отправлена.'
            ],
            'action' => [
                'type' => null,
                'url'  => null
            ]
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request) {
        return response()->json([
            'status'  => 'error',
            'alert'   => [
                'type'  => 'error',
                'text' => 'При отправке ссылки произошел сбой.'
            ],
            'actions' => [
                'type' => null,
                'url'  => null
            ],
        ], 400);
    }
}
