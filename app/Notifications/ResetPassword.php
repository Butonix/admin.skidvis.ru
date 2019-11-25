<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class ResetPassword extends Notification {
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * @var bool
     */
    protected $isApi;

    /**
     * @var string
     */
    protected $domain;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * ResetPassword constructor.
     * @param             $token
     * @param bool        $isApi
     * @param null|string $domain
     */
    public function __construct($token, bool $isApi, ?string $domain) {
        $this->token = $token;
        $this->isApi = $isApi;
        $this->domain = $domain;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed $notifiable
     * @return array|string
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        if ($this->isApi) {
            $domain = (isset($this->domain)) ? $this->domain : 'https://skidvis.ru';
            $passwordResetLink = $domain
                . '/password/reset/'
                . $this->token
                . '?email=' . $notifiable->getEmailForPasswordReset();
        } else {
            $uri = route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset()
            ], false);
            $passwordResetLink = url(config('app.url') . $uri);
        }

        return (new MailMessage)->subject(Lang::getFromJson('Reset Password Notification'))
                                ->line(Lang::getFromJson('You are receiving this email because we received a password reset request for your account.'))
                                ->action(Lang::getFromJson('Reset Password'), $passwordResetLink)
                                ->line(Lang::getFromJson('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.users.expire')]))
                                ->line(Lang::getFromJson('If you did not request a password reset, no further action is required.'));
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure $callback
     * @return void
     */
    public static function toMailUsing($callback) {
        static::$toMailCallback = $callback;
    }
}
