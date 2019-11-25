<?php

namespace App\Notifications;

use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class NewUser extends Notification {
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    //public function __construct(string $password) {
    public function __construct() {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable) {
        //return ['mail', SmscRuChannel::class];
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  User $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable) {
        return (new MailMessage)->subject('Регистрация нового пользователя')
                                ->greeting('Здравствуйте, ' . $notifiable->getFirstName() . '!')
                                ->line('Благодарим вас с выбор «Скидвис».')
                                ->line('Подтвердите ваш email для завершения регистрации.')
                                ->action('Подтвердить Email', $this->verificationUrl($notifiable));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable) {
        return URL::temporarySignedRoute('verification.verify', Carbon::now()
                                                                      ->addMinutes(Config::get('auth.verification.expire', 60)), ['id' => $notifiable->getKey()]);
    }

    /**
     * @param $notifiable
     * @return mixed
     */
    public function toSmscRu($notifiable) {
        ///**
        // * @var Sms $sms
        // */
        //$sms = $this->request->sms()->get()[0];
        //$smsText = $sms->getText();
        //
        //if (strlen($smsText) > 64) {
        //    $result = mb_substr($smsText, 0, 63);
        //} else {
        //    $result = $smsText;
        //}
        //
        //$message = SmscRuMessage::create($result);
        //
        //return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [//
        ];
    }
}
