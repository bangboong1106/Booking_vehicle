<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 9/24/18
 * Time: 20:55
 */

namespace App\Http\Controllers\Api;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Exports\OrdersExport;
use App\Http\Controllers\Base\ApiController;
use App\Model\Entities\FcmToken;
use App\Model\Entities\GpsSyncLog;
use App\Model\Entities\NotificationLogDriver;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\AlertLogRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FcmTokenRepository;
use App\Repositories\GoodsTypeRepository;
use App\Repositories\GoodsUnitRepository;
use App\Repositories\NotificationLogClientRepository;
use App\Repositories\NotificationLogDriverRepository;
use App\Repositories\NotificationLogRepository;
use App\Repositories\Driver\OrderDriverRepository;
use App\Repositories\RepairTicketRepository;
use App\Repositories\ReportScheduleRepository;
use App\Repositories\VehicleFileRepository;
use App\Repositories\VehicleRepository;
use App\Services\NotificationService;
use Carbon\Carbon;
use DateTime;
use Exception;
use FCM;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Mail;

//use Storage;
use Illuminate\Support\Facades\Storage;
use SoapClient;
use SoapHeader;
use Validator;
use Input;
use Spatie\Geocoder\Geocoder;

class NotificationApiController extends ApiController
{
    protected $orderRepos;
    protected $documentRepos;
    protected $repairTicketRepos;
    protected $notificationService;

    /**
     * @return mixed
     */
    public function getOrderRepository()
    {
        return $this->orderRepos;
    }

    /**
     * @param mixed $orderRepos
     */
    public function setOrderRepository($orderRepos): void
    {
        $this->orderRepos = $orderRepos;
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

    /**
     * @return mixed
     */
    public function getRepairTicketRepos()
    {
        return $this->repairTicketRepos;
    }

    /**
     * @param mixed $repairTicketRepos
     */
    public function setRepairTicketRepos($repairTicketRepos): void
    {
        $this->repairTicketRepos = $repairTicketRepos;
    }

    /**
     * @return mixed
     */
    public function getNotificationService()
    {
        return $this->notificationService;
    }

    /**
     * @param mixed $notificationService
     */
    public function setNotificationService($notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    public function __construct(
        OrderDriverRepository $orderRepository,
        DocumentRepository $documentRepository,
        RepairTicketRepository $repairTicketRepository,
        NotificationService $notificationService
    )
    {
        parent::__construct();
        $this->setOrderRepository($orderRepository);
        $this->setDocumentRepos($documentRepository);
        $this->setRepairTicketRepos($repairTicketRepository);
        $this->setNotificationService($notificationService);
    }

    /** Th???ng k?? ????n h??ng v?? ch???ng t??? ch??a ho??n th??nh cho t??i x???
     *  Th??ng b??o cho t??i x??? v??o 07:00 v?? 20:00
     * @param $hourType
     */
    public function pushNoticeDriver($hourType)
    {
        $title = "Th???ng k?? t??nh tr???ng c??ng vi???c h??ng ng??y c???a b???n";
        $driverNotices = [];
        if ($hourType == 1) {
            $orderNotices = $this->getOrderRepository()->getOrderNoticeForDriver();
            $documentNotices = $this->getDocumentRepos()->getDocumentNoticeForDriver();

            foreach ($orderNotices as $item) {
                if ($item->total_complete != 0 || $item->total_pending != 0)
                    $driverNotices[$item->driver_id] = [
                        'order_complete' => $item->total_complete,
                        'order_pending' => $item->total_pending
                    ];
            }
            foreach ($documentNotices as $item) {
                if (isset($orderNotices[$item->driver_id])) {
                    $driverNotices[$item->driver_id]['document_late'] = $item->total_late;
                    $driverNotices[$item->driver_id]['document_pending'] = $item->total_pending;
                } else {
                    if ($item->total_late != 0 || $item->total_pending != 0)
                        $driverNotices[$item->driver_id] = [
                            'document_late' => $item->total_late,
                            'document_pending' => $item->total_pending
                        ];
                }
            }

            foreach ($driverNotices as $driverId => $notice) {
                $message = "";
                if (isset($notice['order_pending']) && $notice['order_pending'] > 0) {
                    $message .= "S??? l?????ng ????n c???n ho??n th??nh : " . $notice['order_pending'] . " ????n h??ng\n";
                }
                if (isset($notice['document_late']) && $notice['document_late'] > 0) {
                    $message .= "S??? l?????ng ch???ng t??? qu?? h???n : " . $notice['document_late'] . " ch???ng t???\n";
                }
                if (isset($notice['document_pending']) && $notice['document_pending'] > 0) {
                    $message .= "S??? l?????ng ch???ng t??? s???p ?????n h???n : " . $notice['document_pending'] . " ch???ng t???";
                }
                $driverIds[0] = $driverId;
                if (!empty($message))
                    $this->getNotificationService()->pushNotificationToAppDriver($driverIds, $title, $message);
                unset($driverIds);
            }
        } else if ($hourType == 2) {
            $orderNotices = $this->getOrderRepository()->getOrderNoticeForDriver();
            foreach ($orderNotices as $item) {
                if ($item->total_complete != 0 || $item->total_pending != 0)
                    $driverNotices[$item->driver_id] = [
                        'order_complete' => $item->total_complete,
                        'order_pending' => $item->total_pending,
                    ];
            }

            foreach ($driverNotices as $driverId => $notice) {
                $message = "";
                if (isset($notice['order_complete']) && $notice['order_complete'] > 0) {
                    $message .= "S??? l?????ng ????n ???? ho??n th??nh : " . $notice['order_complete'] . " ????n h??ng\n";
                }
                if (isset($notice['order_pending']) && $notice['order_pending'] > 0) {
                    $message .= "S??? l?????ng ????n c???n ho??n th??nh : " . $notice['order_pending'] . "  ????n h??ng\n";
                }
                $driverIds[0] = $driverId;
                if (!empty($message))
                    $this->getNotificationService()->pushNotificationToAppDriver($driverIds, $title, $message);
            }
        }
    }

    /**
     * Th???ng k?? t??nh tr???ng ph??? t??ng c???a xe c???n b???o d?????ng cho t??i x??? v?? ??i???u h??nh
     */
    public function pushNoticeAccessory()
    {
        $driverNotices = [];
        $repairNotices = $this->getRepairTicketRepos()->getRepairAccessoryNotice(2);
        foreach ($repairNotices as $item) {
            $key = $item->driver_id . '|' . $item->reg_no;
            $driverNotices[$key][] = [
                'accessory_name' => $item->accessory_name,
                'next_repair_date' => $item->next_repair_date
            ];
        }

        foreach ($driverNotices as $key => $accessories) {
            $driverId = explode("|", $key)[0];
            $regNo = explode("|", $key)[1];

            $title = "Th???ng k?? t??nh tr???ng ph??? t??ng xe " . $regNo . " c???n b???o d?????ng";
            $message = "";
            foreach ($accessories as $accessory) {
                if (isset($accessory['accessory_name']) && isset($accessory['next_repair_date']))
                    $message .= $accessory['accessory_name'] . " : " . AppConstant::convertDate($accessory['next_repair_date'], 'd-m-Y') . "\n";
            }

            $driverIds[0] = $driverId;
            if (!empty($message)) {
                $this->getNotificationService()->pushNotificationToAppDriver($driverIds, $title, $message);
            }
        }
    }
}
