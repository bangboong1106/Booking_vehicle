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
use App\Exports\ReportSchedulerExport;
use App\Http\Controllers\Base\ApiController;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\AlertLogRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DistanceDailyReportRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FcmTokenRepository;
use App\Repositories\NotificationLogDriverRepository;
use App\Repositories\NotificationLogRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\RepairTicketRepository;
use App\Repositories\ReportScheduleRepository;
use App\Repositories\VehicleFileRepository;
use App\Services\NotificationService;
use Carbon\Carbon;
use DateTime;
use Exception;
use FCM;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use JWTAuth;
use Mail;

class ReportScheduleController extends ApiController
{
    protected $fcmTokenRepos;
    protected $alertLogRepos;
    protected $customerRepos;
    protected $notificationLogRepos;
    protected $driverLogRepos;
    protected $driverRepos;
    protected $reportScheduleRepos;
    protected $vehicleFileRepository;
    protected $adminUserInfoRepository;
    protected $distanceDailyReportRepository;
    protected $repairTicketRepository;
    protected $partnerRepository;
    protected $notificationService;


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

    public function getDistanceDailyReportRepository()
    {
        return $this->distanceDailyReportRepository;
    }

    public function setDistanceDailyReportRepository($distanceDailyReportRepository)
    {
        $this->distanceDailyReportRepository = $distanceDailyReportRepository;
    }

    /**
     * @return mixed
     */
    public function getRepairTicketRepository()
    {
        return $this->repairTicketRepository;
    }

    /**
     * @param mixed $repairTicketRepository
     */
    public function setRepairTicketRepository($repairTicketRepository): void
    {
        $this->repairTicketRepository = $repairTicketRepository;
    }

    /**
     * @return mixed
     */
    public function getPartnerRepository()
    {
        return $this->partnerRepository;
    }

    /**
     * @param mixed $partnerRepository
     */
    public function setPartnerRepository($partnerRepository): void
    {
        $this->partnerRepository = $partnerRepository;
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

    public function __construct(FcmTokenRepository $fcmTokenRepository, AlertLogRepository $alertLogRepository,
                                NotificationLogDriverRepository $driverLogRepository, DriverRepository $driverRepository,
                                CustomerRepository $customerRepository, NotificationLogRepository $notificationLogRepository,
                                ReportScheduleRepository $reportScheduleRepos, VehicleFileRepository $vehicleFileRepository,
                                AdminUserInfoRepository $adminUserInfoRepository, DistanceDailyReportRepository $distanceDailyReportRepository,
                                RepairTicketRepository $repairTicketRepository, PartnerRepository $partnerRepository,
                                NotificationService $notificationService)
    {
        parent::__construct();
        $this->setFcmTokenRepos($fcmTokenRepository);
        $this->setAlertLogRepos($alertLogRepository);
        $this->setCustomerRepos($customerRepository);
        $this->setNotificationLogRepos($notificationLogRepository);
        $this->setDriverLogRepos($driverLogRepository);
        $this->setDriverRepos($driverRepository);
        $this->setReportScheduleRepos($reportScheduleRepos);
        $this->setVehicleFileRepository($vehicleFileRepository);
        $this->setAdminUserInfoRepository($adminUserInfoRepository);
        $this->setDistanceDailyReportRepository($distanceDailyReportRepository);
        $this->setRepairTicketRepository($repairTicketRepository);
        $this->setPartnerRepository($partnerRepository);
        $this->setNotificationService($notificationService);
    }

    /**
     * Báo cáo đặt lịch
     * @param $emailTitle
     * @param $email
     * @param $type
     * @param $dateFrom
     * @param $dateTo
     * @param $reportType
     * @param $scheduleType
     */
    public function reportScheduleEmail($emailTitle, $email, $type, $dateFrom, $dateTo, $reportType, $scheduleType)
    {
        try {
            if (!empty($email) && $reportType) {
                $emails = explode(",", $email);
                $emails = array_map('trim', $emails);
                $emails = array_filter($emails);

                $fileInteractive = null;
                if (strpos($reportType, "1") !== false) {
                    $reportSchedulerExport = new ReportSchedulerExport($this->getReportScheduleRepos());
                    $fileInteractive = $reportSchedulerExport->exportReportInteractive($type, $dateFrom, $dateTo);
                }

                $fileVehiclePerformance = null;
                if (strpos($reportType, "2") !== false) {
                    $reportSchedulerExport = new ReportSchedulerExport($this->getReportScheduleRepos());
                    $fileVehiclePerformance = $reportSchedulerExport->exportVehiclePerformance($dateFrom, $dateTo);
                }

                $fileCustomerRevenueAndCost = null;
                if (strpos($reportType, "3") !== false) {
                    $reportSchedulerExport = new ReportSchedulerExport($this->getReportScheduleRepos());
                    $fileCustomerRevenueAndCost = $reportSchedulerExport->exportCustomer($dateFrom, $dateTo, null, null);
                }

                $fileVehicleTeam = null;
                if (strpos($reportType, "4") !== false) {
                    $reportSchedulerExport = new ReportSchedulerExport($this->getReportScheduleRepos());
                    $fileVehicleTeam = $reportSchedulerExport->exportVehicleTeam($dateFrom, $dateTo, null, null);
                }

                $fileInteractiveDriver = null;
                if (strpos($reportType, "5") !== false) {
                    $reportSchedulerExport = new ReportSchedulerExport($this->getReportScheduleRepos());
                    $fileInteractiveDriver = $reportSchedulerExport->exportReportInteractiveDriver($scheduleType);
                }

                $domain = getBackendDomain();

                $data = array(
                    'domain' => !empty($domain) ? $domain : 'https://onelog.com.vn',
                    'date' => isset($date) ? date('d-m-Y') : $dateTo,
                    'fileInteractive' => $fileInteractive,
                    'fileVehiclePerformance' => $fileVehiclePerformance,
                    'fileCustomerRevenueAndCost' => $fileCustomerRevenueAndCost,
                    'fileVehicleTeam' => $fileVehicleTeam,
                    'fileInteractiveDriver' => $fileInteractiveDriver
                );


                Mail::send('layouts.backend.elements.email.report_schedule_mail', $data, function ($message)
                use (
                    $emailTitle, $emails, $reportType, $fileInteractive, $fileVehiclePerformance, $fileCustomerRevenueAndCost, $fileVehicleTeam,
                    $fileInteractiveDriver
                ) {
                    $message->to($emails)->subject($emailTitle);
                    $message->from(env('MAIL_FROM', 'report@onelog.com.vn'), 'Báo cáo ');
                    if (strpos($reportType, "1") !== false) {
                        $message->attach($fileInteractive);
                    }
                    if (strpos($reportType, "2") !== false) {
                        $message->attach($fileVehiclePerformance);
                    }
                    if (strpos($reportType, "3") !== false) {
                        $message->attach($fileCustomerRevenueAndCost);
                    }
                    if (strpos($reportType, "4") !== false) {
                        $message->attach($fileVehicleTeam);
                    }
                    if (strpos($reportType, "5") !== false) {
                        $message->attach($fileInteractiveDriver);
                    }
                });
            }
        } catch (Exception $e) {
            logError('Title : ' . $emailTitle . '- FROM : ' . env('MAIL_FROM', 'report@onelog.com.vn')
                . '- To : ' . $email . '-' . $e);
        }
    }

    function lastDayOf($period, DateTime $date = null)
    {
        $period = strtolower($period);
        $validPeriods = array('year', 'quarter', 'month', 'week');

        if (!in_array($period, $validPeriods))
            throw new Exception('Period must be one of: ' . implode(', ', $validPeriods));

        $newDate = ($date === null) ? new DateTime() : clone $date;

        switch ($period) {
            case 'year':
                $newDate->modify('last day of december ' . $newDate->format('Y'));
                break;
            case 'quarter':
                $month = $newDate->format('n');

                if ($month < 4) {
                    $newDate->modify('last day of march ' . $newDate->format('Y'));
                } elseif ($month > 3 && $month < 7) {
                    $newDate->modify('last day of june ' . $newDate->format('Y'));
                } elseif ($month > 6 && $month < 10) {
                    $newDate->modify('last day of september ' . $newDate->format('Y'));
                } elseif ($month > 9) {
                    $newDate->modify('last day of december ' . $newDate->format('Y'));
                }
                break;
            case 'month':
                $newDate->modify('last day of this month');
                break;
            case 'week':
                $newDate->modify(($newDate->format('w') === '0') ? 'now' : 'sunday this week');
                break;
        }

        return $newDate;
    }

    protected function reportSchedule()
    {
        try {
            $emailTitle = config('constant.APP_NAME') . ' Report: Báo cáo ';
            $dateFrom = '';
            $dateTo = '';
            $type = 'day';
            $report = app('App\Repositories\ReportScheduleRepository')->getReportScheduleByTime();
            if (isset($report) && !empty($report)) {
                foreach ($report as $item) {
                    switch ($item->schedule_type) {
                        case '0': //Hang ngay
                            $dateFrom = date('Y-m-d');
                            $dateTo = date('Y-m-d');
                            $emailTitle = $emailTitle . ' ' . ' ngày ' . date('d-m-Y');
                            break;
                        case '1': // Hang tuan
                            if (date('D') == 'Fri') {
                                // TODO
                                $dateTo = date('Y-m-d');
                                $dateFrom = date('Y-m-d', strtotime('-7 day', strtotime($dateTo)));

                                $emailTitle = $emailTitle . ' ' . ' tuần từ ' . date('d-m-Y') . ' đến ' . date('d-m-Y', strtotime('-7 day', strtotime($dateTo)));
                            }
                            break;
                        case '2': // Hang thang
                            if (gmdate('t') == gmdate('d')) {
                                $dateTo = date('Y-m-d');
                                $dateFrom = date('Y-m-d', strtotime('-1 month', strtotime($dateTo)));
                                $emailTitle = $emailTitle . ' ' . ' tháng từ ' . $dateTo . ' đến ' . $dateFrom;
                            }
                            break;
                        case '3': // Hang quy
                            $type = 'month';
                            $lastDateOfQuarter = $this->lastDayOf('quarter', null);
                            $lastDate = $lastDateOfQuarter->format('Y-m-d');
                            $currentDate = date('Y-m-d');
                            if ($lastDate == $currentDate) {
                                $dateTo = date('Y-m-d');
                                $dateFrom = date('Y-m-d', strtotime('-3 month', strtotime($dateTo)));
                                $emailTitle = $emailTitle . ' ' . ' quý từ ' . $dateTo . ' đến ' . $dateFrom;
                            }
                            break;
                        case '4': // Hang nam
                            $type = 'month';
                            $dateTo = date('Y-m-d');
                            $dateFrom = date('Y-m-d', strtotime('-1 year', strtotime($dateTo)));
                            $emailTitle = $emailTitle . ' ' . ' năm từ ' . $dateTo . ' đến ' . $dateFrom;
                            break;
                    }
                    if (!empty($dateFrom) && !empty($dateTo)) {
                        $this->reportScheduleEmail($emailTitle, $item->email, $type, $dateFrom, $dateTo, $item->report_type);
                    }
                }
            }
        } catch (\Exception $exception) {
            logError($exception);
        }
    }

    public function doReportSchedule(Request $request)
    {
        try {
            $emailTitle = config('constant.APP_NAME') . ' Report: Báo cáo ';
            $dateFrom = '';
            $dateTo = '';
            $type = 'day';
            $report = app('App\Repositories\ReportScheduleRepository')->getReportScheduleByTime();
            if (isset($report) && !empty($report)) {
                foreach ($report as $item) {
                    switch ($item->schedule_type) {
                        case '0': //Hang ngay
                            $dateFrom = date('Y-m-d');
                            $dateTo = date('Y-m-d');
                            $emailTitle = $emailTitle . 'ngày ' . date('d-m-Y');
                            break;
                        case '1': // Hang tuan
                            if (date('D') == 'Fri') {
                                // TODO
                                $dateTo = date('Y-m-d');
                                $dateFrom = date('Y-m-d', strtotime('-7 day', strtotime($dateTo)));

                                $emailTitle = $emailTitle . 'tuần từ ' . date('d-m-Y') . ' đến ' . date('d-m-Y', strtotime('-7 day', strtotime($dateTo)));
                            }
                            break;
                        case '2': // Hang thang
                            if (gmdate('t') == gmdate('d')) {
                                $dateTo = date('Y-m-d');
                                $dateFrom = date('Y-m-d', strtotime('-1 month', strtotime($dateTo)));
                                $emailTitle = $emailTitle . 'tháng từ ' . $dateTo . ' đến ' . $dateFrom;
                            }
                            break;
                        case '3': // Hang quy
                            $type = 'month';
                            $lastDateOfQuarter = $this->lastDayOf('quarter', null);
                            $lastDate = $lastDateOfQuarter->format('Y-m-d');
                            $currentDate = date('Y-m-d');
                            if ($lastDate == $currentDate) {
                                $dateTo = date('Y-m-d');
                                $dateFrom = date('Y-m-d', strtotime('-3 month', strtotime($dateTo)));
                                $emailTitle = $emailTitle . 'quý từ ' . $dateTo . ' đến ' . $dateFrom;
                            }
                            break;
                        case '4': // Hang nam
                            $type = 'month';
                            $dateTo = date('Y-m-d');
                            $dateFrom = date('Y-m-d', strtotime('-1 year', strtotime($dateTo)));
                            $emailTitle = $emailTitle . 'năm từ ' . $dateTo . ' đến ' . $dateFrom;
                            break;
                    }
                    if (!empty($dateFrom) && !empty($dateTo)) {
                        $this->reportScheduleEmail($emailTitle, $item->email, $type, $dateFrom, $dateTo, $item->report_type);
                    }
                }
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => ''
            ]);

        } catch (\Exception $exception) {
            logError($exception);
            return response()->json(['errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)]);
        }
    }


    /**
     * Gửi email thống kê số lượng xe hết hạn giấy phép ngày và xe đến hạn bảo dưỡng
     */
    public function vehicleWarningScheduleEmail()
    {
        try {
            $vehicleWarning = $this->getVehicleFileRepository()->getVehicleFileWarning();
            $vehicleRepairWarning = $this->getDistanceDailyReportRepository()->getVehicleRepairWarning();
            $vehicleAccessoryWarning = [];
            $repairNotices = $this->getRepairTicketRepository()->getRepairAccessoryNotice(1);
            foreach ($repairNotices as $item) {
                $vehicleAccessoryWarning[$item->reg_no][] = [
                    'accessory_name' => $item->accessory_name,
                    'next_repair_date' => $item->next_repair_date];
            }

            if ((!empty($vehicleWarning) && sizeof($vehicleWarning) > 0) ||
                (!empty($vehicleRepairWarning) && sizeof($vehicleRepairWarning) > 0)) {

                //Gửi cho từng đối tác vận tải
                $partners = $this->getPartnerRepository()->search([])->get();
                foreach ($partners as $partner) {
                    $emails = $this->getAdminUserInfoRepository()->getEmailUserListByPartner($partner->id);

                    if (!empty($emails)) {
                        $vehicleWarningByPartner = array_filter(
                            $vehicleWarning,
                            function ($val, $key) use ($partner) {
                                return $partner->id == $val->partner_id;
                            },
                            ARRAY_FILTER_USE_BOTH
                        );
                        $vehicleRepairWarningByPartner = array_filter(
                            $vehicleRepairWarning,
                            function ($val, $key) use ($partner) {
                                return $partner->id == $val->partner_id;
                            },
                            ARRAY_FILTER_USE_BOTH
                        );

                        if ((!empty($vehicleWarningByPartner) && sizeof($vehicleWarningByPartner) > 0) ||
                            (!empty($vehicleRepairWarningByPartner) && sizeof($vehicleRepairWarningByPartner) > 0)) {
                            //Gửi mail
                            $this->doVehicleWarningScheduleEmail($emails, $vehicleWarningByPartner, $vehicleRepairWarningByPartner, $vehicleAccessoryWarning, config('constant.APP_NAME') . ' - Cảnh báo hết hạn giấy tờ và bảo dưỡng xe');

                            //Bắn notify admin cảnh báo giấy tờ xe
                            if (!empty($vehicleWarningByPartner) && sizeof($vehicleWarningByPartner) > 0) {
                                $titleNotification = 'Cảnh báo hết hạn giấy tờ xe';
                                $messageNotification = 'Xe sắp hết hạn giấy tờ: ';
                                $actionIds = '';
                                for ($i = 0; $i < sizeof($vehicleWarningByPartner); $i++) {
                                    if ($i == sizeof($vehicleWarningByPartner) - 1) {
                                        $messageNotification = $messageNotification . $vehicleWarningByPartner[$i]->reg_no;
                                        $actionIds = $vehicleWarningByPartner[$i]->id;
                                    } else {
                                        $messageNotification = $messageNotification . ($vehicleWarningByPartner[$i]->reg_no . ', ');
                                        $actionIds = $vehicleWarningByPartner[$i]->id . ',';
                                    }
                                }
                                $userIds = $this->getAdminUserInfoRepository()->getPartnerUserForNotifyById($partner->id);
                                $this->getNotificationService()->pushNotificationWeb($titleNotification, $messageNotification, AppConstant::NOTIFICATION_SCREEN_VEHICLE, $userIds, $actionIds, 'admin');
                            }

                            //Bắn notify admin cảnh báo bảo dưỡng xe
                            if ((!empty($vehicleRepairWarningByPartner) && sizeof($vehicleRepairWarningByPartner) > 0)) {
                                $titleNotification = 'Cảnh báo bảo dưỡng xe';
                                $messageNotification = 'Xe sắp đến hạn bảo dưỡng: ';
                                $actionIds = '';
                                for ($i = 0; $i < sizeof($vehicleRepairWarningByPartner); $i++) {
                                    if ($i == sizeof($vehicleRepairWarningByPartner) - 1) {
                                        $messageNotification = $messageNotification . $vehicleRepairWarningByPartner[$i]->reg_no;
                                        $actionIds = $vehicleRepairWarningByPartner[$i]->id;
                                    } else {
                                        $messageNotification = $messageNotification . ($vehicleRepairWarningByPartner[$i]->reg_no . ', ');
                                        $actionIds = $vehicleRepairWarningByPartner[$i]->id . ',';
                                    }
                                }
                                $userIds = $this->getAdminUserInfoRepository()->getPartnerUserForNotifyById($partner->id);
                                $this->getNotificationService()->pushNotificationWeb($titleNotification, $messageNotification, AppConstant::NOTIFICATION_SCREEN_VEHICLE_REPAIR, $userIds, $actionIds, 'admin');
                            }
                        }

                    }
                }
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $vehicleWarning
            ]);
        } catch (\Exception $exception) {
            logError($exception);
            return response()->json(['errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)]);
        }
    }

    public function doVehicleWarningScheduleEmail($emails, $vehicles, $vehicleRepairs, $vehicleAccessoryWarning, $emailTitle)
    {
        $data = array(
            'date' => Carbon::now()->format('d/m/Y'),
            'data' => $vehicles,
            'dataRepair' => $vehicleRepairs,
            'dataAccessoryRepair' => $vehicleAccessoryWarning
        );
        Mail::send('layouts.backend.elements.email.warning_vehicle_schedule_mail', $data, function ($message) use ($emailTitle, $emails) {
            $message->to($emails)->subject($emailTitle);
            $message->from('report@onelog.com.vn', 'Báo cáo');
        });
    }

    public function processDataUpload(Request $request)
    {
        try {
            $now = Carbon::now();
            logError('********************* processDataUpload Google Drive: ' . $now->toDateTimeString(), []);

            // $googleDisk = Storage::disk('google');
            // Create file du lieu Template
            app('App\Http\Controllers\Backend\OrderController')->exportTemplate();
            $uploadedFile = File::get(public_path('file/export/orderTemplate.xlsx'));

            if (null != $uploadedFile) {
                $dir = '/';
                $recursive = false;
                $contents = collect(Storage::cloud()->listContents($dir, $recursive));
                $dirS = $contents->where('type', '=', 'dir')
                    ->where('filename', '=', env("FOLDER_DOMAIN"))
                    ->first();
                if (!$dirS) {
                    Storage::cloud()->createDir(env("FOLDER_DOMAIN"));
                    $contents = collect(Storage::cloud()->listContents($dir, $recursive));
                    $dirS = $contents->where('type', '=', 'dir')
                        ->where('filename', '=', env("FOLDER_DOMAIN"))
                        ->first();
                }
                $path = $dirS['path'] . '/' . 'Danh_sach_don_hang';

                $files = collect(Storage::cloud()->listContents($dirS['path'], $recursive));
                $exist = $files->where('type', '=', 'file')
                    ->where('filename', '=', 'Danh_sach_don_hang')
                    ->first();
                if (!$exist) {
                    Storage::cloud()->put($path, $uploadedFile);
                } else {
                    $f = fopen(public_path('file/export/orderTemplate.xlsx'), 'r+');
                    Storage::cloud()->putStream($exist['path'], $f);
                }
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => [
                    'message' => 'ok'
                ]
            ]);
        } catch (\Exception $exception) {
            logError('*********************processDataUpload: ' . $exception->getMessage(), []);
            return response()->json(['errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)]);
        }
    }
}