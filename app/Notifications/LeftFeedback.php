<?php

namespace App\Notifications;

use App\Models\Feedbacks\Feedback;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class LeftFeedback extends Notification {
    use Queueable;

    /**
     * @var Feedback
     */
    protected $feedback;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    //public function __construct(string $password) {
    public function __construct(Feedback $feedback) {
        $this->feedback = $feedback;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable) {
        //return ['mail', SmscRuChannel::class];
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  User $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable) {
        return (new MailMessage)->subject('Перезвоните мне')
                                ->greeting('Новая заявка «' . $this->feedback->getFeedbackTypeName() . '».')
                                ->line('Имя: ' . $this->feedback->getName() . ', телефон: ' . $this->feedback->getPhone());
    }

    /**
     * @param $notifiable
     *
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
     *
     * @return array
     */
    public function toArray($notifiable) {
        return [
            'feedbackType' => $this->feedback->getFeedbackTypeName(),
            'name'         => $this->feedback->getName(),
            'phone'        => $this->feedback->getPhone(),
        ];
    }
}
