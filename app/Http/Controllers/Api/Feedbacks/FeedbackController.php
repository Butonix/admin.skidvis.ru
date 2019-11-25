<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 07.08.2019
 * Time: 23:39
 */

namespace App\Http\Controllers\Api\Feedbacks;

use App\Http\Controllers\Controller;
use App\Models\Feedbacks\Feedback;
use App\Models\Users\User;
use App\Notifications\LeftFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller {
    /**
     * @var
     */
    protected $feedbacks;

    /**
     * @var User
     */
    protected $users;

    /**
     * FeedbackController constructor.
     *
     * @param Feedback $feedbacks
     * @param User     $users
     */
    public function __construct(Feedback $feedbacks, User $users) {
        $this->feedbacks = $feedbacks;
        $this->users = $users;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        /**
         * @var Feedback $feedback
         */
        $validator = Validator::make($request->all(), Feedback::getRules(), Feedback::getMessages());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $frd = $request->only(['name', 'phone', 'typeId']);
        $typeId = $frd['typeId'] ?? 1;
        $feedback = $this->feedbacks->create($frd);
        $feedback->setUserIp($request->ip());
        $feedback->setUserAgent($request->userAgent());
        $feedback->feedbackType()->associate($typeId);
        $feedback->save();

        if (Feedback::isNotificationEnabled()) {
            Notification::send($this->users->notifiableUsers()->get(), new LeftFeedback($feedback));
        }

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Заявка успешно сохранена. С вами свяжутся в ближайшее доступное время.',
            ],
            'action' => [
                'type' => null,
                'url'  => null,
            ],
        ], 200);
    }
}
