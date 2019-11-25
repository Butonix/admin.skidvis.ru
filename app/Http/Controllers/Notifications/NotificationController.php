<?php
/**
 * Created by PhpStorm.
 * User: Pavel Onokhov
 * Date: 26.08.2019
 * Time: 12:49
 */

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Feedbacks\Feedback;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller {
    /**
     * @var DatabaseNotification
     */
    protected $notifications;

    /**
     * NotificationController constructor.
     *
     * @param DatabaseNotification $notifications
     */
    public function __construct(DatabaseNotification $notifications) {
        $this->notifications = $notifications;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $frd = $request->all();
        $notifications = \Auth::user()
                              ->notifications()
                              ->paginate($frd['perPage'] ?? $this->notifications->getPerPage());
        //
        //foreach ($notifications as $notification) {
        //    dump($notification->read_at, $notification->data);
        //}
        //dd(123123);

        return view('notifications.index', compact('frd', 'notifications'));
    }

    /**
     * @param Request              $request
     * @param DatabaseNotification $notification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeRead(Request $request, DatabaseNotification $notification) {
        $notification->markAsRead();
        $notification->save();

        return response()->json([
            'status' => 'OK',
            'alert'  => [
                'type' => 'success',
                'text' => 'Уведомление успешно отмечено как прочитанное',
            ],
        ], 200);
    }

    /**
     * @param Request              $request
     * @param DatabaseNotification $notification
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, DatabaseNotification $notification) {
        $notification->delete();

        return redirect()->back()->with('flash_message', [
            'type' => 'success',
            'text' => 'Уведомление успешно удалено',
        ]);
    }
}
