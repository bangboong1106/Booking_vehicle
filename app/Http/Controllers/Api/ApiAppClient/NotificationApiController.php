<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 9/24/18
 * Time: 20:55
 */

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ClientApiController;
use App\Model\Entities\FcmToken;
use App\Model\Entities\GpsSyncLog;
use App\Model\Entities\NotificationLogDriver;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\AlertLogRepository;
use App\Repositories\Client\NotificationClientRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FcmTokenRepository;
use App\Repositories\NotificationLogClientRepository;
use App\Repositories\NotificationLogDriverRepository;
use App\Repositories\NotificationLogRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReportScheduleRepository;
use App\Repositories\VehicleFileRepository;
use App\Repositories\VehicleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Mail;
use Exception;

//use Storage;
use Illuminate\Support\Facades\Storage;
use SoapClient;
use SoapHeader;
use Validator;
use Input;

class NotificationApiController extends ClientApiController
{
    protected $fcmTokenRepos;
    protected $alertLogRepos;
    protected $customerRepos;
    protected $notificationLogRepos;
    protected $driverLogRepos;
    protected $notificationLogClientRepos;
    protected $driverRepos;
    protected $reportScheduleRepos;
    protected $vehicleFileRepository;
    protected $adminUserInfoRepository;

    protected $orderRepos;
    protected $vehicleRepos;
    protected $documentRepos;

    public function getFcmTokenRepos()
    {
        return $this->fcmTokenRepos;
    }

    public function setFcmTokenRepos($fcmTokenRepos)
    {
        $this->fcmTokenRepos = $fcmTokenRepos;
    }

    public function getAlertLogRepos()
    {
        return $this->alertLogRepos;
    }

    public function setAlertLogRepos($alertLogRepos)
    {
        $this->alertLogRepos = $alertLogRepos;
    }

    public function getCustomerRepos()
    {
        return $this->customerRepos;
    }

    public function setCustomerRepos($customerRepos)
    {
        $this->customerRepos = $customerRepos;
    }

    public function getNotificationLogRepos()
    {
        return $this->notificationLogRepos;
    }

    public function setNotificationLogRepos($notificationLogRepos)
    {
        $this->notificationLogRepos = $notificationLogRepos;
    }

    public function getDriverLogRepos()
    {
        return $this->driverLogRepos;
    }

    public function setDriverLogRepos($driverLogRepos)
    {
        $this->driverLogRepos = $driverLogRepos;
    }

    public function getDriverRepos()
    {
        return $this->driverRepos;
    }

    public function setDriverRepos($driverRepos)
    {
        $this->driverRepos = $driverRepos;
    }

    public function getReportScheduleRepos()
    {
        return $this->reportScheduleRepos;
    }

    public function setReportScheduleRepos($reportScheduleRepos)
    {
        $this->reportScheduleRepos = $reportScheduleRepos;
    }

    public function getVehicleFileRepository()
    {
        return $this->vehicleFileRepository;
    }

    public function setVehicleFileRepository($vehicleFileRepository)
    {
        $this->vehicleFileRepository = $vehicleFileRepository;
    }

    public function getAdminUserInfoRepository()
    {
        return $this->adminUserInfoRepository;
    }

    public function setAdminUserInfoRepository($adminUserInfoRepository)
    {
        $this->adminUserInfoRepository = $adminUserInfoRepository;
    }

    public function getOrderRepos()
    {
        return $this->orderRepos;
    }

    public function setOrderRepos($orderRepos)
    {
        $this->orderRepos = $orderRepos;
    }

    public function getVehicleRepos()
    {
        return $this->vehicleRepos;
    }

    public function setVehicleRepos($vehicleRepos)
    {
        $this->vehicleRepos = $vehicleRepos;
    }

    public function getNotificationLogClientRepos()
    {
        return $this->notificationLogClientRepos;
    }

    public function setNotificationLogClientRepos($notificationLogClientRepos)
    {
        $this->notificationLogClientRepos = $notificationLogClientRepos;
    }

    /**
     * @return mixed
     */
    public function getDocumentRepos()
    {
        return $this->documentRepos;
    }

    /**
     * @param mixed $documentRepos
     */
    public function setDocumentRepos($documentRepos): void
    {
        $this->documentRepos = $documentRepos;
    }


    public function __construct(
        NotificationClientRepository $notificationClientRepository,
        FcmTokenRepository $fcmTokenRepository,
        AlertLogRepository $alertLogRepository,
        NotificationLogDriverRepository $driverLogRepository,
        DriverRepository $driverRepository,
        CustomerRepository $customerRepository,
        NotificationLogRepository $notificationLogRepository,
        ReportScheduleRepository $reportScheduleRepos,
        VehicleFileRepository $vehicleFileRepository,
        AdminUserInfoRepository $adminUserInfoRepository,
        OrderRepository $orderRepository,
        VehicleRepository $vehicleRepository,
        NotificationLogClientRepository $notificationLogClientRepository,
        DocumentRepository $documentRepository
    ) {
        parent::__construct($customerRepository);
        $this->setRepository($notificationClientRepository);
        $this->setFcmTokenRepos($fcmTokenRepository);
        $this->setAlertLogRepos($alertLogRepository);
        $this->setCustomerRepos($customerRepository);
        $this->setNotificationLogRepos($notificationLogRepository);
        $this->setDriverLogRepos($driverLogRepository);
        $this->setDriverRepos($driverRepository);
        $this->setReportScheduleRepos($reportScheduleRepos);
        $this->setVehicleFileRepository($vehicleFileRepository);
        $this->setAdminUserInfoRepository($adminUserInfoRepository);
        $this->setOrderRepos($orderRepository);
        $this->setVehicleRepos($vehicleRepository);
        $this->setNotificationLogClientRepos($notificationLogClientRepository);
        $this->setDocumentRepos($documentRepository);
    }

    /*FCM for site kh*/
    public function updateTokenFcmForClient(Request $request)
    {
        $token = $request->request->get('token');
        $user = Auth::User();
        if (null != $user && !empty($token)) {
            $userId = $user->id;
            if (!$this->getFcmTokenRepos()->checkExistUserIdAndToken($userId, $token)) {
                $fcmToken = new FcmToken();
                $fcmToken->fcm_token = $token;
                $fcmToken->user_id = $userId;
                $fcmToken->platform_type = AppConstant::PLATFORM_TYPE_WEB;
                $fcmToken->save();
            }
        }
    }

    public function pushNotificationToWebCustomer($userIds, $title, $message, $actionScreen, $actionId)
    {
        try {
            if (!empty($userIds) && is_array($userIds)) {
                //Lưu log
                foreach ($userIds as $id) {
                    $notificationLog = $this->getNotificationLogClientRepos()->findFirstOrNew([]);
                    $notificationLog->title = $title;
                    $notificationLog->message = $message;
                    $notificationLog->user_id = $id;
                    $notificationLog->action_id = $actionId;
                    $notificationLog->action_screen = $actionScreen;
                    $notificationLog->read_status = AppConstant::NOTIFICATION_UNREAD;
                    $notificationLog->save();
                }

                //Bắn notify
                $this->pushNotificationWeb($userIds, $title, $message, $actionId, $actionScreen, false);
            }
        } catch (\Exception $e) {
            logError($e);
        }
    }

    public function pushNotificationWeb($userIds, $title, $message, $actionId, $actionScreen, $webAdmin = true)
    {
        try {
            if (!empty($userIds) && is_array($userIds)) {
                $fcms = $this->getFcmTokenRepos()->getFcmTokenByUserIds($userIds);
                if (!empty($fcms)) {
                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 60);
                    $notificationBuilder = new PayloadNotificationBuilder($title);
                    $notificationBuilder->setBody($message);
                    $notificationBuilder->setSound('default');
                    $notificationBuilder->setIcon('https://onelog.com.vn/ic_launcher.png');
                    $notificationBuilder->setClickAction($webAdmin ? env('APP_URL') . '/' . env('BACKEND_ALIAS') : env('APP_URL') . '/' . 'main#/dashboard');

                    $dataBuilder = new PayloadDataBuilder();
                    $dataBuilder->addData(['actionId' => $actionId]);
                    $dataBuilder->addData(['actionScreen' => $actionScreen]);
                    $dataBuilder->addData(['title' => $title]);
                    $dataBuilder->addData(['imageUrl' => '']);
                    $dataBuilder->addData(['message' => $message]);
                    $dataBuilder->addData(['webAdmin' => $webAdmin]);

                    $option = $optionBuilder->build();
                    $notification = $notificationBuilder->build();
                    $data = $dataBuilder->build();

                    $downstreamResponse = FCM::sendTo($fcms, $option, $notification, $data);
                    $tokensToDelete = $downstreamResponse->tokensToDelete();
                    if (!empty($tokensToDelete)) {
                        $tokens = $this->getFcmTokenRepos()->getFcmFullByTokens($tokensToDelete);
                        if (!empty($tokens)) {
                            foreach ($tokens as $t) {
                                $t->delete();
                            }
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

    public function list(Request $request)
    {
        try {
            $userId = Auth::User()->id;
            $data = $this->getNotificationLogClientRepos()->getNotificationUnreadForUser($userId, Request::get('pageIndex'), Request::get('isViewUnRead'));

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (\Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
    //đọc tất cả thông báo
    public function readAllNotificationLogForCustomer()
    {
        try {
            $userId = Auth::User()->id;
            $result = $this->getNotificationLogClientRepos()->readAllNotificationLogForCustomer($userId);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $result
            ]);
        } catch (\Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function updateNotificationLog(Request $request)
    {
        $id = $request->request->get('id');
        $notificationLog = $this->getNotificationLogClientRepos()->findFirstOrNew(['id' => $id]);
        if ($notificationLog != null) {
            $notificationLog->read_status = AppConstant::NOTIFICATION_READ;
            $notificationLog->save();
        }
    }

    // API lấy tổng thông báo chưa đọc
    // CreatedBy nlhoang 16/06/2020
    public function totalUnread(Request $request)
    {
        try {
            $userId = Auth::User()->id;
            $total = $this->getRepository()->getTotalUnread($userId);

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $total
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
    /*End fcm site kh*/
}
