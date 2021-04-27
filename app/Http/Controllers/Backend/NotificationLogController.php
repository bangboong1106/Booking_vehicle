<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\FcmToken;
use App\Repositories\FcmTokenRepository;
use App\Repositories\NotificationLogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * Class NotificationLogController
 * @package App\Http\Controllers\Backend
 */
class NotificationLogController extends BackendController
{
    protected $fcmTokenRepo;
    protected $notificationLogRepo;

    public function getFcmTokenRepo()
    {
        return $this->fcmTokenRepo;
    }

    public function setFcmTokenRepo($fcmTokenRepo)
    {
        $this->fcmTokenRepo = $fcmTokenRepo;
    }

    public function getNotificationLogRepo()
    {
        return $this->notificationLogRepo;
    }

    public function setNotificationLogRepo($notificationLogRepo)
    {
        $this->notificationLogRepo = $notificationLogRepo;
    }

    public function __construct(NotificationLogRepository $notificationLogRepository, FcmTokenRepository $fcmTokenRepository)
    {
        parent::__construct();
        $this->setRepository($notificationLogRepository);
        $this->setNotificationLogRepo($notificationLogRepository);
        $this->setFcmTokenRepo($fcmTokenRepository);
        $this->setTitle(trans('models.notification_log.name'));
        $this->setMenu('setting');
    }

    // Display Notification popup
    public function displayNotification(Request $request)
    {
        $data = null;
        $payload = $request->request->get('payload');
        if (null != $payload) {
            $data['actionScreen'] = $payload['data']['actionScreen'];
            $data['actionId'] = $payload['data']['actionId'];
            $data['title'] = $payload['data']['title'];
            $data['message'] = $payload['data']['message'];
        }
        return view('layouts.backend.elements.fcm.notification_item', [
            'notification' => (object)$data
        ]);
    }

    public function getNotification()
    {
        $data = null;
        $user = $this->getCurrentUser();
        $data = null;
        if (!empty($user) && !empty($user->id)) {
            $data = $this->getNotificationForUser($user->id, false);
        }
        return view('layouts.backend.elements.structures.notification', [
            'notification' => (object)$data
        ]);
    }

    // Display Notification popup
    public function makeReadAllNotification(Request $request)
    {
        $user = $this->getCurrentUser();
        $data = null;
        if (!empty($user) && !empty($user->id)) {
            $this->doMakeReadAll($user->id);
            $data = $this->getNotificationForUser($user->id, false);
        }

        return view('layouts.backend.elements.fcm.notification_modal', [
            'notification' => (object)$data
        ]);
    }

    // Receive notification on web browser
    public function urlUpdateNotification(Request $request)
    {
        $user = $this->getCurrentUser();
        $data = null;
        if (!empty($user) && !empty($user->id)) {
            $data = $this->getNotificationForUser($user->id, false);
            $payload = $request->request->get('payload');
            if (null != $payload) {
                $actionScreen = $payload['data']['actionScreen'];
                $actionId = $payload['data']['actionId'];
                $title = $payload['data']['title'];
                $message = $payload['data']['message'];

                $data['actionScreen'] = $actionScreen;
                $data['actionId'] = $actionId;
                $data['title'] = $title;
                $data['message'] = $message;
            }
        }

        return view('layouts.backend.elements.fcm.notification_modal', [
            'notification' => (object)$data
        ]);
    }

    // update fcm token
    public function updateTokenFcm(Request $request)
    {
        $token = $request->request->get('token');
        $user = $this->getCurrentUser();
        if (null != $user && !empty($token)) {
            $userId = $user->id;
            if (!$this->getFcmTokenRepo()->checkExistUserIdAndToken($userId, $token)) {
                $fcmToken = new FcmToken();
                $fcmToken->fcm_token = $token;
                $fcmToken->user_id = $userId;
                $fcmToken->platform_type = AppConstant::PLATFORM_TYPE_WEB;
                $fcmToken->save();
            }
        }
    }

    // Click to notification item in header
    public function clickToNotificationItem($id)
    {
        $notificationItem = $this->getNotificationLogRepo()->find($id);
        if (null == $notificationItem) {
            return redirect('admin');
        }

        if (AppConstant::NOTIFICATION_UNREAD == $notificationItem->read_status) {
            $notificationItem->read_status = AppConstant::NOTIFICATION_READ;
            $notificationItem->save();
        }
        if (AppConstant::NOTIFICATION_SCREEN_ORDER == $notificationItem->action_screen) {
            return 0 < $notificationItem->action_id ? \Redirect::route('order.show', $notificationItem->action_id) : \Redirect::route('order.index');
        } else if (AppConstant::NOTIFICATION_SCREEN_VEHICLE == $notificationItem->action_screen) {
            return 0 < $notificationItem->action_id ? \Redirect::route('vehicle.show', $notificationItem->action_id) : \Redirect::route('vehicle.index');
        } else if (AppConstant::NOTIFICATION_SCREEN_DRIVER == $notificationItem->action_screen) {
            return 0 < $notificationItem->action_id ? \Redirect::route('driver.show', $notificationItem->action_id) : \Redirect::route('driver.index');
        } else if (AppConstant::NOTIFICATION_SCREEN_CLIENT == $notificationItem->action_screen) {
            return 0 < $notificationItem->action_id ? \Redirect::route('customer.show', $notificationItem->action_id) : \Redirect::route('customer.index');
        } else if (AppConstant::NOTIFICATION_SCREEN_ROUTE == $notificationItem->action_screen) {
            return 0 < $notificationItem->action_id ? \Redirect::route('route.show', $notificationItem->action_id) : \Redirect::route('route.index');
        }

        return redirect('admin');
    }

    //Update read status notification
    public function updateReadNotify(Request $request)
    {
        try {
            $id = $request->get('id');

            $user = $this->getCurrentUser();
            $notificationLog = $this->getRepository()->getNotificationLogById($user->id, $id);
            if ($notificationLog) {
                $notificationLog->read_status = AppConstant::NOTIFICATION_READ;
                $notificationLog->save();
            }
            $data = $this->getNotificationForUser($user->id, false);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'countUnread' => $data['countUnread']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
}
