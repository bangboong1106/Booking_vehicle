<?php

namespace App\Services;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FcmTokenRepository;
use App\Repositories\GoodsTypeRepository;
use App\Repositories\NotificationLogClientRepository;
use App\Repositories\NotificationLogDriverRepository;
use App\Repositories\NotificationLogRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class NotificationService
{
    protected $_fcmTokenRepository;
    protected $_notifyLogRepository;
    protected $_notifyLogClientRepository;
    protected $_notifyLogDriverRepository;
    protected $_adminUserInfoRepository;
    protected $_driverRepository;
    protected $_routeRepository;

    public function __construct(FcmTokenRepository $fcmTokenRepository,
                                NotificationLogRepository $notificationLogRepository,
                                NotificationLogClientRepository $notificationLogClientRepository,
                                NotificationLogDriverRepository $notificationLogDriverRepository,
                                AdminUserInfoRepository $adminUserInfoRepository,
                                DriverRepository $driverRepository)
    {
        $this->_fcmTokenRepository = $fcmTokenRepository;
        $this->_notifyLogRepository = $notificationLogRepository;
        $this->_notifyLogClientRepository = $notificationLogClientRepository;
        $this->_notifyLogDriverRepository = $notificationLogDriverRepository;
        $this->_adminUserInfoRepository = $adminUserInfoRepository;
        $this->_driverRepository = $driverRepository;
    }

    //Notify cho C20, Đối tác vận tải, Chủ hàng, Khách hàng
    public function pushNotificationWeb($title, $message, $actionScreen, $userIds, $actionId = 0, $webType = 'admin')
    {
        try {
            if (empty($userIds))
                return;

            //Lưu log notify
            foreach ($userIds as $userId) {
                if ($webType == 'admin') {
                    // Save notification to admin
                    $notificationLog = $this->_notifyLogRepository->findFirstOrNew([]);
                    $notificationLog->title = $title;
                    $notificationLog->content = $message;
                    $notificationLog->user_id = $userId;
                    $notificationLog->action_id = $actionId;
                    $notificationLog->action_screen = $actionScreen;
                    $notificationLog->read_status = AppConstant::NOTIFICATION_UNREAD;
                    $notificationLog->save();
                } else if ($webType == 'client') {
                    $notificationLog = $this->_notifyLogClientRepository->findFirstOrNew([]);
                    $notificationLog->title = $title;
                    $notificationLog->message = $message;
                    $notificationLog->user_id = $userId;
                    $notificationLog->action_id = $actionId;
                    $notificationLog->action_screen = $actionScreen;
                    $notificationLog->read_status = AppConstant::NOTIFICATION_UNREAD;
                    $notificationLog->save();
                }
            }

            //Băn notify
            $fcmTokens = $this->_fcmTokenRepository->getFcmTokenWebByUserIds($userIds)->toArray();
            if (!empty($fcmTokens)) {
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 60);
                $notificationBuilder = new PayloadNotificationBuilder($title);
                $notificationBuilder->setBody($message);
                $notificationBuilder->setSound('default');
                $notificationBuilder->setChannelId('default');
                $notificationBuilder->setIcon('https://onelog.com.vn/ic_launcher.png');
                $notificationBuilder->setClickAction($webType == 'admin' ? env('APP_URL') . '/' . env('BACKEND_ALIAS') : env('APP_URL') . '/' . 'main#/dashboard');

                $dataBuilder = new PayloadDataBuilder();
                $actionId = 1;
                $dataBuilder->addData(['actionId' => $actionId]);
                $dataBuilder->addData(['actionScreen' => $actionScreen]);
                $dataBuilder->addData(['title' => $title]);
                $dataBuilder->addData(['imageUrl' => '']);
                $dataBuilder->addData(['message' => $message]);
                $dataBuilder->addData(['webAdmin' => $webType == 'admin' ? true : false]);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                $downstreamResponse = FCM::sendTo($fcmTokens, $option, $notification, $data);
                $tokensToDelete = $downstreamResponse->tokensToDelete();
                if (!empty($tokensToDelete)) {
                    $tokens = $this->_fcmTokenRepository->getFcmFullByTokens($tokensToDelete);
                    if (!empty($tokens)) {
                        foreach ($tokens as $t) {
                            $t->delete();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function pushNotificationToApp($title, $message, $fcmTokens, $entity, $data)
    {
        if (!empty($fcmTokens)) {

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 60);
            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($message);
            $notificationBuilder->setSound('default');
            $notificationBuilder->setIcon('ic_launcher.png');

            //   $openActivity = $this->getOpenActivityPath($entity);
            // TODO: Logic linking app tu notification
            //                    $notificationBuilder->setClickAction($openActivity);

            $dataBuilder = new PayloadDataBuilder();
            if ($entity)
                $dataBuilder->addData(['orderStr' => $entity]);
            if (isset($data['driver_id']))
                $dataBuilder->addData(['notificationDriverId' => $data['driver_id']]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            $downstreamResponse = FCM::sendTo($fcmTokens, $option, $notification, $data);
            $tokensToDelete = $downstreamResponse->tokensToDelete();
            if (!empty($tokensToDelete)) {
                $tokens = $this->_fcmTokenRepository->getFcmFullByTokens($tokensToDelete);
                if (!empty($tokens)) {
                    foreach ($tokens as $t) {
                        $t->delete();
                    }
                }
            }
        }
    }

    public function pushNotificationToAppDriver($driverIds, $title, $message, $entity = null)
    {
        try {
            if (!empty($driverIds) && is_array($driverIds)) {

                // Save notification to driver
                $logDriver = $this->_notifyLogDriverRepository->findFirstOrNew([]);
                $logDriver->title = $title;
                $logDriver->message = $message;
                if ($entity)
                    $logDriver->data = json_encode($entity);
                $logDriver->driver_id = $driverIds[0];
                $logDriver->read_status = '0';
                $logDriver->save();

                //Bắn notification
                $fcmTokens = $this->_fcmTokenRepository->getFcmTokenByDriverIds($driverIds)->toArray();
                $this->pushNotificationToApp($title, $message, $fcmTokens, $entity, ['driver_id' => $driverIds[0]]);

            }
        } catch (\Exception $exception) {
            logError($exception . '-Data: ' . json_encode($driverIds));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function pushNotificationToAppByUser($userIds, $title, $message, $entity = null)
    {
        try {
            if (!empty($userIds) && is_array($userIds)) {
                //Bắn notification
                $fcmTokens = $this->_fcmTokenRepository->getFcmTokenAppByUserIds($userIds)->toArray();
                $this->pushNotificationToApp($title, $message, $fcmTokens, $entity, ['user_id' => $userIds[0]]);

            }
        } catch (\Exception $exception) {
            logError($exception . '-Data: ' . json_encode($userIds));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function getOpenActivityPath($order)
    {
        $path = 'com.onelog.elogistics.main.ui.main.MainActivity';
        if (!empty($order)) {
            $obj = json_decode($order);
            switch ($obj->status) {
                case config("constant.CHO_NHAN_HANG"):
                    $path = 'com.onelog.elogistics.main.ui.main.order_detail.OrderDetailActivity';
                    break;
            }
        }
        return $path;
    }

    //Client gửi thông báo Chủ hàng
    public function notifyClientToCustomer($type, $userIds, $data)
    {
        $clientName = Auth::user()->customer ? Auth::User()->customer->full_name : '';
        $title = AppConstant::generateTitlePN($clientName);

        $message = '';
        //Đặt đơn
        if ($type == 1) {
            $message = 'Khách hàng ' . $clientName . ' đã yêu cầu đơn đặt hàng ' . $data['order_customer_no'];
        }

        //Sửa đơn
        if ($type == 2) {
            $message = 'Khách hàng ' . $clientName . ' đã sửa đơn đặt hàng ' . $data['order_customer_no'];
        }


        $this->pushNotificationWeb($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER_CUSTOMER, $userIds, $data['order_customer_id'], 'client');
        $this->pushNotificationToAppByUser($userIds, $title, $message);
    }

    //Chủ hàng gửi thông báo Khách hàng
    public function notifyCustomerToClient($type, $userIds, $data)
    {
        $customerName = Auth::user()->customer ? Auth::User()->customer->full_name : '';
        $title = AppConstant::generateTitlePN($customerName);

        $message = '';
        //Xác nhận
        if ($type == 1) {
            $message = 'Chủ hàng ' . $customerName . ' đã xác nhận đơn đặt hàng ' . $data['order_customer_no'];
        }

        //Yêu cầu sửa
        if ($type == 2) {
            $message = 'Chủ hàng ' . $customerName . ' đã yêu cầu sửa đơn đặt hàng ' . $data['order_customer_no'];
        }

        //Hủy đơn
        if ($type == 3) {
            $message = 'Chủ hàng ' . $customerName . ' đã hủy đơn đặt hàng ' . $data['order_customer_no'];
        }

        //Chủ hàng tạo đơn
        if ($type == 4) {
            $message = 'Chủ hàng ' . $customerName . ' đã tạo đơn đặt hàng ' . $data['order_customer_no'];
        }

        //Chủ hàng sửa đơn
        if ($type == 5) {
            $message = 'Chủ hàng ' . $customerName . ' đã sửa đơn đặt hàng ' . $data['order_customer_no'];
        }

        $this->pushNotificationWeb($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER_CUSTOMER, $userIds, $data['order_customer_id'], 'client');
        $this->pushNotificationToAppByUser($userIds, $title, $message);
    }

    //Chủ hàng gửi thông báo C20
    public function notifyCustomerToC20($type, $userIds, $data)
    {
        $customerName = Auth::user()->customer ? Auth::User()->customer->full_name : '';
        $title = AppConstant::generateTitlePN($customerName);

        $message = '';
        //Xuất hàng
        if ($type == 1) {
            $message = 'Chủ hàng ' . $customerName . ' đã yêu cầu vận chuyển cho đơn đặt hàng ' . $data['order_customer_no'];
        }

        $this->pushNotificationWeb($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER_CUSTOMER, $userIds, $data['order_customer_id'], 'admin');
        $this->pushNotificationToAppByUser($userIds, $title, $message);
    }

    //C20, Partner, Tài xế gửi thông báo cho chủ hàng và khách hàng
    public function notifyToCustomerAndClient($orderCustomer)
    {
        $customerIds = [$orderCustomer->customer_id, $orderCustomer->client_id];
        $userIds = DB::table('customer')->whereIn('id', $customerIds)->get()->pluck('user_id')->toArray();
        $title = AppConstant::generateTitlePN();
        $message = '';
        switch ($orderCustomer->status) {
            case config("constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG"):
                $message = 'Đơn đặt hàng ' . $orderCustomer->order_no . ' đã được tạo';
                break;
            case config("constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN"):
                $message = 'Đơn đặt hàng ' . $orderCustomer->order_no . ' đang được vận chuyển';
                break;
            case config("constant.ORDER_CUSTOMER_STATUS.HOAN_THANH"):
                $message = 'Đơn đặt hàng ' . $orderCustomer->order_no . ' đã hoàn thành';
                break;
            case config("constant.ORDER_CUSTOMER_STATUS.C20_HUY"):
            case config("constant.ORDER_CUSTOMER_STATUS.CHU_HANG_HUY"):
                $message = 'Đơn đặt hàng ' . $orderCustomer->order_no . ' đã bị hủy';
                break;
        }
        $this->pushNotificationWeb($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER_CUSTOMER, $userIds, $orderCustomer->id, 'client');
        $this->pushNotificationToAppByUser($userIds, $title, $message, $orderCustomer);
    }

    //C20 gửi thông báo cho đối tác vận tải
    public function notifyC20ToPartner($type, $userIds, $data)
    {
        $title = AppConstant::generateTitlePN();
        $message = '';

        //C20 gán đối tác vận tải
        if ($type == 1) {
            $message = 'Bạn đã được phân công phụ trách đơn vận tải ' . $data['order_code'];
        }

        //C20 gán đối tác vận tải khác , thông báo hủy đơn
        if ($type == 2) {
            $message = 'Đơn vận tải ' . $data['order_code'] . ' đã bị hủy';
        }

        //Thay đổi thông tin giao nhận
        if ($type == 3) {
            $message = 'Đơn vận tải ' . $data['order_code'] . ' đã thay đổi thông tin giao nhận và trả hàng';
        }

        $this->pushNotificationWeb($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER, $userIds, $data['order_id'], 'admin');
    }

    //C20, Partner gửi thông báo cho tài xế
    public function notifyC20OrPartnerToDriver($type, $driverIds, $orderEntity)
    {
        $message = '';
        if (Auth::user()->role == 'admin') {
            $title = AppConstant::generateTitlePN();
        } else {
            $partnerName = Auth::user()->partner ? Auth::User()->partner->full_name : '';
            $title = AppConstant::generateTitlePN($partnerName);
        }
        $orderCode = $orderEntity->order_code;

        //Gán đơn cho tài xế
        if ($type == 1) {
            $message = 'Bạn đã được phân công phụ trách đơn vận tải ' . $orderCode;
        }

        //Thông báo hủy đơn
        if ($type == 2) {
            $message = 'Đơn vận tải ' . $orderCode . ' đã bị hủy';
        }

        //Thay đổi thông tin giao nhận
        if ($type == 3) {
            $message = 'Đơn vận tải ' . $orderCode . ' đã thay đổi thông tin giao nhận và trả hàng';
        }

        $orderEntity = $this->_getDataPushFromOrder($orderEntity);
        $this->pushNotificationToAppDriver($driverIds, $title, $message, $orderEntity);
    }

    //Tài xế gửi thông báo C20, đối tác vận tải
    public function notifyDriverToC20AndPartner($type, $data)
    {
        $driver = $this->_driverRepository->getDriverByUserId(Auth::user()->id);
        if (!$driver)
            return;
        $driver_name = $driver->full_name;
        $title = AppConstant::generateTitlePN($driver_name);
        $message = '';
        if ($type == 1) {
            switch ($data['order_status']) {
                case config('constant.SAN_SANG'):
                    $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được tài xế ' . $driver_name . ' từ chối vận chuyển';
                    break;
                case config('constant.CHO_NHAN_HANG'):
                    $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được tài xế ' . $driver_name . ' xác nhận vận chuyển';
                    break;
                case config('constant.DANG_VAN_CHUYEN'):
                    $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được tài xế ' . $driver_name . ' đang vận chuyển';
                    break;
                case config('constant.HOAN_THANH'):
                    $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được tài xế ' . $driver_name . ' hoàn thành';
                    break;
                case config('constant.HUY'):
                    $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được tài xế ' . $driver_name . ' hủy';
                    break;
            }

            $adminUserIds = $this->_adminUserInfoRepository->getAdminUserForNotifyByCustomer($data['customer_id']);
            $partnerUserIds = $this->_adminUserInfoRepository->getPartnerUserForNotifyById($data['partner_id']);
            $userIds = array_merge($adminUserIds, $partnerUserIds);
            $this->pushNotificationWeb($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER, $userIds, $data['order_id'], 'admin');
            $this->pushNotificationToAppByUser($userIds, $title, $message);
        }

        //Cập nhật chi phí
        if ($type == 2) {
            $route = $this->_routeRepository->getItemById($data['route_id']);
            if ($route) {
                $message = "Tài xế " . $driver_name . " đã cập nhật chi phí của chuyến xe "
                    . $route->route_code . " (" . $route->name . ")";
                $userIds = array_unique([$route->ins_id, $route->upd_id, $route->approved_id]);
                $this->pushNotificationWeb($title, $message, AppConstant::NOTIFICATION_SCREEN_ROUTE, $userIds, $data['route_id'], 'admin');
                $this->pushNotificationToAppByUser($userIds, $title, $message);
            }
        }

    }

    //Thông báo từ Đối tác vận tải đến C20
    public function notifyPartnerToC20($type, $data)
    {
        $message = '';
        $partnerName = Auth::user()->partner ? Auth::User()->partner->full_name : '';
        $title = AppConstant::generateTitlePN($partnerName);

        if ($type == 1) {
            $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được đối tác ' . $partnerName . ' xác nhận';
        }

        if ($type == 2) {
            $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được đối tác ' . $partnerName . ' tạo chuyến';
        }

        if ($type == 3) {
            $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được đối tác ' . $partnerName . ' yêu cầu sửa đơn hàng';
        }

        if ($type == 4) {
            $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được đối tác ' . $partnerName . ' hủy';
        }

        if ($type == 5) {
            $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được đối tác ' . $partnerName . ' hoàn thành';
        }

        if ($type == 6) {
            $message = 'Đơn vận tải ' . $data['order_code'] . ' đã được đối tác ' . $partnerName . ' đổi chuyến';
        }

        $userIds = $this->_adminUserInfoRepository->getAllUserIsAdmin()->pluck('id')->toArray();
        $this->pushNotificationWeb($title, $message, AppConstant::NOTIFICATION_SCREEN_ORDER, $userIds, $data['order_id'], 'admin');
        $this->pushNotificationToAppByUser($userIds, $title, $message);
    }

    public function _getDataPushFromOrder($order)
    {
        $location_destination = $order->locationDestination;
        $location_arrival = $order->locationArrival;
        $goods_type = '';
        if (!empty($order->listGoods)) {
            $goods_type = implode(',', array_column($order->listGoods->toArray(), "title"));
        }

        $data = [
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'status' => $order->status,
            'order_date' => $order->order_date,
            'customer_name' => $order->customer ? $order->customer->full_name : '-',
            'customer_mobile_no' => $order->customer_mobile_no ? $order->customer_mobile_no : '',
            'ETD_date' => $order->ETD_date,
            'ETD_time' => $order->ETD_time,
            'contact_name_destination' => $order->contact_name_destination,
            'contact_mobile_no_destination' => $order->contact_mobile_no_destination,
            'location_destination' => $location_destination == null ? "" :
                (empty($location_destination->full_address) ? $location_destination->title : $location_destination->full_address),
            'latitude_destination' => $location_destination == null ? "" : $location_destination->latitude,
            'longitude_destination' => $location_destination == null ? "" : $location_destination->longitude,
            'ETA_date' => $order->ETA_date,
            'ETA_time' => $order->ETA_time,
            'contact_name_arrival' => $order->contact_name_arrival,
            'contact_mobile_no_arrival' => $order->contact_mobile_no_arrival,
            'location_arrival' => $location_arrival == null ? "" :
                (empty($location_arrival->full_address) ? $location_arrival->title : $location_arrival->full_address),
            'latitude_arrival' => $location_arrival == null ? "" : $location_arrival->latitude,
            'longitude_arrival' => $location_arrival == null ? "" : $location_arrival->longitude,
            'goods_type' => $goods_type,
            'amount' => $order->amount,
            'quantity' => $order->quantity,
            'volume' => $order->volume,
            'weight' => $order->weight,
            'precedence' => config('constant.ORDER_PRECEDENCE_NORMAL'),
            'note' => $order->note,
            'insured_goods' => null == $order->insured_goods ? "0" : $order->insured_goods,
            'loading_arrival_fee' => null == $order->loading_arrival_fee ? "0" : $order->loading_arrival_fee,
            'loading_destination_fee' => null == $order->loading_destination_fee ? "0" : $order->loading_destination_fee
        ];

        return $data;
    }
}