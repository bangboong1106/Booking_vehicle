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
use App\Repositories\NotificationLogClientRepository;
use App\Repositories\NotificationLogDriverRepository;
use App\Repositories\NotificationLogRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReportScheduleRepository;
use App\Repositories\VehicleFileRepository;
use App\Repositories\VehicleRepository;
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

class AlertLogApiController extends ApiController
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
        $this->setOrderRepos($orderRepository);
        $this->setVehicleRepos($vehicleRepository);
        $this->setNotificationLogClientRepos($notificationLogClientRepository);
        $this->setDocumentRepos($documentRepository);
    }

    public function reportScheduleEmail($emailTitle, $email, $date, $reports)
    {
        $items = collect($reports['data'])->groupBy('entity_name');
        $data = array(
            'date' => isset($date) ? date('d-m-Y') : $date,
            'data' => $items,
            'summary' => $reports['summary']
        );
        Mail::send('layouts.backend.elements.email.report_schedule_mail', $data, function ($message) use ($emailTitle, $email) {
            $message->to($email, $email)->subject($emailTitle);
            $message->from('report@'.config('constant.APP_COMPANY').'.com.vn', 'Báo cáo');
        });
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

    public function pushNotificationReportSchedule(Request $request)
    {
        try {
            $emailTitle = config('constant.APP_NAME').' Report: Báo cáo ';
            $dateFrom = '';
            $dateTo = '';
            $type = 'day';
            $report = $this->getReportScheduleRepos()->getReportScheduleByTime();
            if (isset($report) && !empty($report)) {
                foreach ($report as $item) {
                    switch ($item->schedule_type) {
                        case '0': //Hang ngay
                            $dateFrom = date('Y-m-d');
                            $dateTo = date('Y-m-d');
                            $emailTitle = $emailTitle . ' ngày ' . date('d-m-Y');
                            break;
                        case '1': // Hang tuan
                            if (date('D') == 'Fri') {
                                // TODO
                                $dateTo = date('Y-m-d');
                                $dateFrom = date('Y-m-d', strtotime('-7 day', strtotime($dateTo)));

                                $emailTitle = $emailTitle . ' tuần từ ' . date('d-m-Y') . ' đến ' . date('d-m-Y', strtotime('-7 day', strtotime($dateTo)));
                            }
                            break;
                        case '2': // Hang thang
                            if (gmdate('t') == gmdate('d')) {
                                $dateTo = date('Y-m-d');
                                $dateFrom = date('Y-m-d', strtotime('-1 month', strtotime($dateTo)));
                                $emailTitle = $emailTitle . ' tháng từ ' . $dateTo . ' đến ' . $dateFrom;
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
                                $emailTitle = $emailTitle . ' quý từ ' . $dateTo . ' đến ' . $dateFrom;
                            }
                            break;
                        case '4': // Hang nam
                            $type = 'month';
                            $dateTo = date('Y-m-d');
                            $dateFrom = date('Y-m-d', strtotime('-1 year', strtotime($dateTo)));
                            $emailTitle = $emailTitle . ' năm từ ' . $dateTo . ' đến ' . $dateFrom;
                            break;
                    }
                    if (!empty($dateFrom) && !empty($dateTo)) {
                        $data = app('App\Repositories\ReportScheduleRepository')->reportSchedule($type, $dateFrom, $dateTo);
                        $this->reportScheduleEmail($emailTitle, $item->email, $dateTo, $data);
                    }
                }
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => ''
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function pushNotification(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), []);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $this->syncGPS();

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'message' => 'ok'
                    ]
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    protected function syncGPS()
    {
        $enableGps = env('GPS_STATUS', false);
        if ($enableGps) {
            $GCs = explode(',', env('GPS_COMPANY'));
            foreach ($GCs as $gc) {
                $gpsSyncLog = new GpsSyncLog();
                if ($gc == config('constant.GC_BINH_ANH')) {
                    try {
                        // Initialize WS with the WSDL
                        $client = new SoapClient(env('GPS_BINH_ANH_WEB_SERVICE_WSDL', 'http://gps4.binhanh.com.vn/WebServices/BinhAnh.asmx?wsdl'));
                        $params = array(
                            'xnCode' => env('GPS_BINH_ANH_THANH_DAT_USER', '7213'),
                            'key' => env('GPS_BINH_ANH_THANH_DAT_KEY', 'pUrgARkgRakh4ZBAJqRdHCPKBTGMtf3KZdjU2fUA')
                        );
                        $gpsSyncLog->request = json_encode(['request' => $params]);
                        $response = $client->__soapCall(env('GPS_BINH_ANH_THANH_DAT_FUNCTION_NAME', 'GetVehicleInfoWithAddress'), array($params));
                        $gpsSyncLog->response = json_encode(['response' => $response]);

                        if (isset($response)) {
                            $data = $response->GetVehicleInfoWithAddressResult;
                            if (isset($data)) {
                                $gpsSyncLog->error_message = $data->MessageResult;

                                if ('Success' . equalToIgnoringCase($gpsSyncLog->error_message)) {
                                    $gpsSyncLog->response = '';
                                    $this->processGPS($response->GetVehicleInfoWithAddressResult->Vehicles->Vehicle);
                                }
                            }
                        }
                    } catch (\Exception $exception) {
                        $gpsSyncLog->error_code = 'Exception';
                        $gpsSyncLog->error_message = $exception->getMessage();
                    }
                    $gpsSyncLog->type_request = 'ALL';
                    $gpsSyncLog->save();
                } else if ($gc == config('constant.GC_BINH_ANH_2')) {
                    try {
                        $client = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'application/json']]);
                        $request = $client->post('http://api.gps.binhanh.vn/api/gps/tracking', ['body' => json_encode(
                            [
                                'CustomerCode' => env('GPS_BINH_ANH_2_ACCOUNT'),
                                'key' => env('GPS_BINH_ANH_2_KEY')
                            ]
                        )]);
                        $response = $request->getBody();
                        $gpsSyncLog->request = json_encode(['request' => 'http://api.gps.binhanh.vn/api/gps/tracking']);

                        if ($response != null) {
                            $content = $response->getContents();
                            $gpsSyncLog->response = $content;
                            $data = json_decode($content);
                            if (!empty($data)) {
                                $this->processGPS($data->Vehicles);
                            }
                        }
                    } catch (\Exception $exception) {
                        $gpsSyncLog->error_code = 'Exception';
                        $gpsSyncLog->error_message = $exception->getMessage();
                    }
                    $gpsSyncLog->type_request = 'ALL';
                    $gpsSyncLog->save();
                } else if ($gc == config('constant.GC_VIET_MAPS')) {
                    try {
                        $now = Carbon::now()->timestamp;
                        $client = new \GuzzleHttp\Client();
                        $request = $client->get('https://client-api.quanlyxe.vn/v3/tracking/getvehiclestatuses?id=0&ticks=' . $now . '&apikey=' . env('GPS_VIETMAPS_API_KEY'));
                        $response = $request->getBody();
                        $gpsSyncLog->request = json_encode(['request' => 'https://client-api.quanlyxe.vn/v3/tracking/getvehiclestatuses?id=0&ticks=' . $now . '&apikey=' . env('GPS_VIETMAPS_API_KEY')]);

                        if ($response != null) {
                            $content = $response->getContents();
                            $gpsSyncLog->response = $content;
                            $data = json_decode($content);
                            if (!empty($data)) {
                                $this->processGPSByGpsId($data->Data);
                            }
                        }
                    } catch (\Exception $exception) {
                        $gpsSyncLog->error_code = 'Exception';
                        $gpsSyncLog->error_message = $exception->getMessage();
                    }
                    $gpsSyncLog->type_request = 'ALL';
                    $gpsSyncLog->save();
                } else if ($gc == config('constant.GC_ADA')) {
                    try {
                        $client = new \GuzzleHttp\Client(['headers' => ['X-API-KEY' => env('GPS_ADA_API_KEY')]]);
                        $request = $client->get('http://apiv4.adagps.com/index.php/GetTrackingData?username=' . env('GPS_ADA_USERNAME'));
                        $response = $request->getBody();
                        $gpsSyncLog->request = json_encode(['request' => 'http://apiv4.adagps.com/index.php/GetTrackingData?username=' . env('GPS_ADA_USERNAME')]);

                        if ($response != null) {
                            $content = $response->getContents();
                            $gpsSyncLog->response = 'OK';
                            $data = json_decode($content);
                            if (!empty($data)) {
                                $this->processGPSByGpsIdADA($data->data);
                            }
                        }
                    } catch (\Exception $exception) {
                        $gpsSyncLog->error_code = 'Exception';
                        $gpsSyncLog->error_message = $exception->getMessage();
                    }
                    $gpsSyncLog->type_request = 'ALL';
                    $gpsSyncLog->save();
                } else if ($gc == config('constant.GC_EUPFIN')) {
                    try {
                        $client = new \GuzzleHttp\Client(['headers' => ['X-Eupfin-Api-Key' => env('GPS_EUPFIN_API_KEY')]]);
                        $body['account'] = env('GPS_EUPFIN_ACCOUNT');
                        $body['password'] = env('GPS_EUPFIN_PASSWORD');

                        $request = $client->post(env('GPS_EUPFIN_URL_LOCATION', 'http://api.eup.net.vn:8000/thanhdat/realtimeAll'), ['form_params' => $body]);
                        $response = $request->getBody();
                        $gpsSyncLog->request = json_encode(['request' => env('GPS_EUPFIN_URL_LOCATION', 'http://api.eup.net.vn:8000/thanhdat/realtimeAll')]);

                        if ($response != null) {
                            $content = $response->getContents();
                            $gpsSyncLog->response = 'OK';
                            $data = json_decode($content);
                            if (!empty($data)) {
                                $this->processGPSByGpsIdEupfin($data->result);
                            }
                        }
                    } catch (\Exception $exception) {
                        $gpsSyncLog->error_code = 'Exception';
                        $gpsSyncLog->error_message = $exception->getMessage();
                    }
                    $gpsSyncLog->type_request = 'ALL';
                    $gpsSyncLog->save();
                } else if ($gc == config('constant.GC_VCOMSAT')) {
                    try {
                        $client = new SoapClient(env('GPS_VCOMSAT_WEB_SERVICE_WSDL', 'http://stagingws.giamsathanhtrinh.vn/SmartLog.asmx?WSDL'));
                        $auth = array(
                            'Username' => env('GPS_VCOMSAT_USER_HEADER', 'smartlog'),
                            'Password' => env('GPS_VCOMSAT_PASSWORD_HEADER', 'p@ssw0rd'),
                        );
                        $header = new SoapHeader('http://tempuri.org/', 'ServiceAuthHeader', $auth, false);
                        $client->__setSoapHeaders($header);

                        $params = array(
                            'username' => env('GPS_VCOMSAT_USER', 'tinnghia123'),
                            'password' => env('GPS_VCOMSAT_PASSWORD', 'thudo1962'),
                        );
                        $gpsSyncLog->request = json_encode(['request' => $params]);
                        $response = $client->__soapCall(env('GPS_VCOMSAT_FUNCTION_NAME', 'GetCarInfo '), array($params));
                        $gpsSyncLog->response = json_encode(['response' => $response]);

                        if (isset($response)) {
                            $getCarInfoResult = $response->GetCarInfoResult;
                            if (isset($getCarInfoResult)) {
                                $gpsSyncLog->error_message = 'OK';
                                $gpsSyncLog->response = '';
                                $this->processGPSVComSat($getCarInfoResult->CarInfo_SmartLog);
                            }
                        }
                    } catch (\Exception $exception) {
                        $gpsSyncLog->error_code = 'Exception';
                        $gpsSyncLog->error_message = $exception->getMessage();
                    }
                    $gpsSyncLog->type_request = 'ALL';
                    $gpsSyncLog->save();
                } else if ($gc == config('constant.GC_EPOSI')) {
                    try {
                        $client = new \GuzzleHttp\Client();

                        $request = $client->get(env('GPS_EPOSI_URL_ALL', 'http://qc31.vn/rest/api/v2/vehicle/list/state'), ['auth' => [
                            env('GPS_EPOSI_USER', 'giatruong'), env('GPS_EPOSI_PASSWORD', 'giatruong2018')
                        ]]);
                        $response = $request->getBody();
                        $gpsSyncLog->request = json_encode(['request' => env('GPS_EPOSI_URL_ALL', 'http://qc31.vn/rest/api/v2/vehicle/list/state')]);

                        if ($response != null) {
                            $content = $response->getContents();
                            $gpsSyncLog->response = 'OK';
                            $data = json_decode($content);
                            if (!empty($data)) {
                                $this->processGPSByGpsEposi($data->data);
                            }
                        }
                    } catch (\Exception $exception) {
                        $gpsSyncLog->error_code = 'Exception';
                        $gpsSyncLog->error_message = $exception->getMessage();
                    }
                    $gpsSyncLog->type_request = 'ALL';
                    $gpsSyncLog->save();
                } else if ($gc == config('constant.GC_ADSUN')) {
                    try {
                        $client = new \GuzzleHttp\Client();
                        $url = env('GPS_ADSUN_URL_ALL') . env('GPS_ADSUN_COMPANY_ID') . '&username=' . env('GPS_ADSUN_USER') . '&pwd=' . env('GPS_ADSUN_PASSWORD');
                        $request = $client->get($url);
                        $response = $request->getBody();
                        $gpsSyncLog->request = json_encode(['request' => $url]);

                        if ($response != null) {
                            $content = $response->getContents();
                            $gpsSyncLog->response = 'OK';
                            $data = json_decode($content);
                            if (!empty($data)) {
                                $this->processGPSAdsun($data->Data);
                            }
                        }
                    } catch (\Exception $exception) {
                        $gpsSyncLog->error_code = 'Exception';
                        $gpsSyncLog->error_message = $exception->getMessage();
                    }
                    $gpsSyncLog->type_request = 'ALL';
                    $gpsSyncLog->save();
                }
            }
        }
    }

    protected function processGPSByGpsEposi($data)
    {
        foreach ($data as $vehicle) {
            app('App\Repositories\VehicleRepository')->updateGpsVehicleByVehiclePlate(
                $vehicle->id,
                $vehicle->latitude,
                $vehicle->longitude,
                $vehicle->address
            );
        }
    }

    protected function processGPSAdsun($data)
    {
        foreach ($data as $vehicle) {
            if ($vehicle && $vehicle->ToaDo && $vehicle->ToaDo->Lat && $vehicle->ToaDo->Lng) {
                $address = $this->getAddressFromCoordinates($vehicle->ToaDo->Lat, $vehicle->ToaDo->Lng);
                app('App\Repositories\VehicleRepository')->updateGpsVehicleByVehiclePlate(
                    $vehicle->ActualPlate,
                    $vehicle->ToaDo->Lat,
                    $vehicle->ToaDo->Lng,
                    $address ? $address['formatted_address'] : ''
                );
            }
        }
    }

    protected function getAddressFromCoordinates($latitude, $longitude)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $geocoder = new Geocoder($client);
            $geocoder->setApiKey(env('GOOGLE_MAP_API_KEY', ''));

            return $geocoder->getAddressForCoordinates($latitude, $longitude);
        } catch (Exception $exception) {
            logError($exception);
            return '';
        }
    }

    protected function processGPSByGpsIdEupfin($data)
    {
        foreach ($data as $vehicle) {
            app('App\Repositories\VehicleRepository')->updateGpsVehicleByVehiclePlate(
                $vehicle->VehicleNo,
                $vehicle->Latitude,
                $vehicle->Longitude,
                $vehicle->Address
            );
        }
    }

    protected function processGPSByGpsIdADA($data)
    {
        foreach ($data as $vehicle) {
            app('App\Repositories\VehicleRepository')->updateGpsVehicleByGpsId(
                $vehicle->vehicle_id,
                $vehicle->latitude,
                $vehicle->longitude,
                $vehicle->address
            );
        }
    }

    protected function processGPSByGpsId($data)
    {
        foreach ($data as $vehicle) {
            app('App\Repositories\VehicleRepository')->updateGpsVehicleByGpsId(
                $vehicle->Id,
                $vehicle->Y,
                $vehicle->X,
                $vehicle->Address
            );
        }
    }

    protected function processGPSVComSat($data)
    {
        try {
            foreach ($data as $vehicle) {
                app('App\Repositories\VehicleRepository')->updateGpsVehicle(
                    str_replace(array("-", " ", "."), "", $vehicle->CarPlate),
                    $vehicle->Lat,
                    $vehicle->Lng,
                    $vehicle->Address
                );
            }
        } catch (\Exception $exception) {
        }
    }

    protected function processGPS($data)
    {
        try {
            foreach ($data as $vehicle) {
                app('App\Repositories\VehicleRepository')->updateGpsVehicle(
                    $vehicle->VehiclePlate,
                    $vehicle->Latitude,
                    $vehicle->Longitude,
                    $vehicle->Address
                );
            }
        } catch (\Exception $exception) {
            logError($exception);
        }
    }

    public function pushNotificationAdmin($title, $message, $actionScreen, $userIds, $actionId = 0)
    {
        try {
            $fcmTokens = $this->getFcmTokenRepos()->getFcmTokenByRole(['admin','partner'], $userIds);
            if (!empty($fcmTokens)) {
                $users = [];
                foreach ($fcmTokens as $f) {
                    $fcms[] = $f->fcm_token;
                    if (!in_array($f->user_id, $users)) {
                        // Save notification to admin
                        $notificationLog = $this->getNotificationLogRepos()->findFirstOrNew([]);
                        $notificationLog->title = $title;
                        $notificationLog->content = $message;
                        $notificationLog->user_id = $f->user_id;
                        $notificationLog->action_id = $actionId;
                        $notificationLog->action_screen = $actionScreen;
                        $notificationLog->read_status = AppConstant::NOTIFICATION_UNREAD;
                        $notificationLog->save();

                        $users[] = $f->user_id;
                    }
                }

                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 60);
                $notificationBuilder = new PayloadNotificationBuilder($title);
                $notificationBuilder->setBody($message);
                $notificationBuilder->setSound('default');
                $notificationBuilder->setChannelId('default');
                $notificationBuilder->setIcon('https://onelog.com.vn/ic_launcher.png');
                $notificationBuilder->setClickAction(env('APP_URL') . '/' . env('BACKEND_ALIAS', 'admin'));

                $dataBuilder = new PayloadDataBuilder();
                $actionId = 1;
                $dataBuilder->addData(['actionId' => $actionId]);
                $dataBuilder->addData(['actionScreen' => $actionScreen]);
                $dataBuilder->addData(['title' => $title]);
                $dataBuilder->addData(['imageUrl' => '']);
                $dataBuilder->addData(['message' => $message]);
                $dataBuilder->addData(['webAdmin' => true]);

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
        } catch (\Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function pushNotificationToDriver($driverIds, $title, $message, $imageUrl = null, $order = null)
    {
        try {
            if (!empty($driverIds) && is_array($driverIds)) {
                $fcms = $this->getFcmTokenRepos()->getFcmTokenByDriverIds($driverIds)->toArray();
                if (!empty($fcms)) {
                    // Save notification to driver
                    $logDriver = new NotificationLogDriver();
                    $logDriver->title = $title;
                    $logDriver->message = $message;
                    if ($order)
                        $logDriver->data = json_encode($order);
                    $logDriver->driver_id = $driverIds[0];
                    $logDriver->read_status = '0';
                    $logDriver->save();

                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 60);
                    $notificationBuilder = new PayloadNotificationBuilder($title);
                    $notificationBuilder->setBody($message);
                    $notificationBuilder->setSound('default');
                    $notificationBuilder->setIcon('ic_launcher.png');

                    $openActivity = $this->getOpenActivityPath($order);
                    // TODO: Logic linking app tu notification
                    //                    $notificationBuilder->setClickAction($openActivity);

                    $dataBuilder = new PayloadDataBuilder();
                    if ($order)
                        $dataBuilder->addData(['orderStr' => $order]);
                    $dataBuilder->addData(['notificationDriverId' => $logDriver->id]);

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

    public function loadNotification(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'pageSize' => 'required',
                'pageIndex' => 'required',
                'textSearch' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $userId = Auth::User()->id;
                $driver = $this->getDriverRepos()->getDriverByUserId($userId);

                $result = null;
                if (null != $driver) {
                    $result = $this->getDriverLogRepos()->findByDriverId($driver->id, $request);
                }
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $result
                ]);
            }
        } catch (Exception $exception) {
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

    public function pushNotificationWeb($userId = [], $title, $message, $actionId, $actionScreen, $webAdmin = true)
    {
        try {
            if (!empty($userId) && is_array($userId)) {
                $fcms = $this->getFcmTokenRepos()->getFcmTokenByUserIds($userId);
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

    public function readNotificationDriver(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $userId = Auth::User()->id;
                $driver = $this->getDriverRepos()->getDriverByUserId($userId);
                $countUnread = -1;
                if (null != $driver) {
                    $driverLog = $this->getDriverLogRepos()->findByDriverIdAndId($driver->id, $request['id']);
                    if (null != $driverLog) {
                        $driverLog->read_status = AppConstant::NOTIFICATION_READ;
                        $driverLog->save();
                    }
                    $countUnread = $this->getDriverLogRepos()->countUnreadNotification($driver->id);
                }
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'message' => 'ok',
                        'countUnread' => $countUnread < 0 ? null : $countUnread
                    ]
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function readAllNotificationDriver(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), []);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $userId = Auth::User()->id;
                $driver = $this->getDriverRepos()->getDriverByUserId($userId);
                $countUnread = -1;
                if (null != $driver) {
                    $this->getDriverLogRepos()->updateReadAllByDriverId($driver->id);
                    $countUnread = $this->getDriverLogRepos()->countUnreadNotification($driver->id);
                }
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
                        'message' => 'ok',
                        'countUnread' => $countUnread < 0 ? null : $countUnread
                    ]
                ]);
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
}
