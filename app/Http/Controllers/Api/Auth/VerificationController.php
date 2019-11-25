<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request) {
        $response = null;
        ($request->user()->hasVerifiedEmail())
            ? $response = [
                'status' => 'OK',
                'alert' => [
                    'type' => 'warning',
                    'text' => 'Email уже подтвержден.',
                ],
                'action' => [
                    'type' => null, //Редирект на главную
                    'url' => null
                ]
            ]
            : $response = [
                'status' => 'OK',
                'alert' => [
                    'type' => 'success',
                    'text' => 'Требуется подтверждение Email.',
                ],
                'action' => [
                    'type' => null, //Редирект на страницу с сообщением о необходимости подтверждения Email
                    'url' => null
                ]
            ];

        return response()->json($response, 200);
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request) {
        if ($request->route('id') != $request->user()->getKey()) {
            return response()->json([
                'status' => 'error',
                'alert' => [
                    'type' => 'error',
                    'text' => 'Проверка авторизации: ссылка и пользователь не соответствуют друг другу.'
                ],
                'action' => [
                    'type' => null,
                    'url' => null
                ]
            ], 422);
        }

        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'OK',
                'alert' => [
                    'type' => 'warning',
                    'text' => 'Email уже подтвержден',
                ],
                'action' => [
                    'type' => null,
                    'url' => null
                ]
            ], 200);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        //return redirect($this->redirectPath())->with('verified', true);
        return response()->json([
            'status' => 'OK',
            'alert' => [
                'type' => 'success',
                'text' => 'Email успешно подтвержден.',
            ],
            'action' => [
                'type' => null, //Редирект на главную
                'url' => null
            ]
        ], 200);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'OK',
                'alert' => [
                    'type' => 'warning',
                    'text' => 'Email уже подтвержден',
                ],
                'action' => [
                    'type' => null,
                    'url' => null
                ]
            ], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => 'OK',
            'alert' => [
                'type' => 'success',
                'text' => 'Письмо с подтверждением отправлено успешно.',
            ],
            'action' => [
                'type' => null,
                'url' => null
            ]
        ], 200);
    }
}
