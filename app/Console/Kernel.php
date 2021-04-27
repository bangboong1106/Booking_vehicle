<?php

namespace App\Console;

use App\Common\AppConstant;
use App\Model\Entities\GpsSyncLog;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Http\Request;
use SoapClient;
use SoapHeader;
use Spatie\Geocoder\Geocoder;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        'App\Console\Commands\OrderJob',
        'App\Console\Commands\GPSSync'
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $this->syncGPS();
        })->everyTenMinutes();

        $schedule->call(function () {
            $this->doReportSchedule();
            $this->doUpdateStatusVehicle();
        })->everyMinute();

        $schedule->call(function () {
            $this->doWarningVehicleSchedule();
        })->dailyAt('10:00');

        // TODO: Bỏ tạo file Google Sheet
//        $schedule->call(function () {
//            $this->doProcessDataUpload();
//        })->dailyAt('05:05');

        $schedule->call(function () {
            $this->doDailyReportByVehicle();
        })->dailyAt('23:55');

        $schedule->call(function () {
            $this->doDailyDistanceReport();
        })->dailyAt('01:00');

        $schedule->call(function () {
            $this->doReportOperatorDaily();
        })->everyFifteenMinutes();

        $schedule->call(function () {
            $this->doCalcStatusDocumentsDaily();
        })->dailyAt('00:01');

        $schedule->call(function () {
            $this->doSyncOrderToPartner();
        })->everyFiveMinutes();

        $schedule->call(function () {
            $this->doLoginTP();
        })->hourlyAt(10);

        $schedule->call(function () {
            $this->doPushNoticeDriver(1);
        })->dailyAt('07:00');

        $schedule->call(function () {
            $this->doPushNoticeDriver(2);
        })->dailyAt('20:00');

        $schedule->call(function () {
            $this->doPushNoticeAccessory();
        })->dailyAt('07:00');

        // Xóa dữ liệu GPS Vinh Hiển. Chỉ lưu trong 3 ngày
        $schedule->call(function () {
            $this->deleteVinhHienGPSData();
        })->dailyAt('03:00');

        $schedule->call(function () {
            $this->doProcessEventInQueue();
        })->everyMinute();
    }

    protected function commands()
    {
        require base_path('routes/console.php');
        $this->load(__DIR__ . '/Commands');
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

    protected function processGPSAdsun($data)
    {
        foreach ($data as $vehicle) {
            if ($vehicle && $vehicle->ToaDo && $vehicle->ToaDo->Lat && $vehicle->ToaDo->Lng) {
                $address = $this->getAddressFromCoordinates($vehicle->ToaDo->Lat, $vehicle->ToaDo->Lng);
                app('App\Repositories\VehicleRepository')->updateGpsVehicleByVehiclePlate(
                    $vehicle->ActualPlate, $vehicle->ToaDo->Lat, $vehicle->ToaDo->Lng, $address ? $address['formatted_address'] : ''
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
        } catch (\Exception $exception) {
            logError($exception);
            return '';
        }
    }

    protected function processGPSByGpsEposi($data)
    {
        foreach ($data as $vehicle) {
            app('App\Repositories\VehicleRepository')->updateGpsVehicleByVehiclePlate(
                $vehicle->id, $vehicle->latitude, $vehicle->longitude, $vehicle->address
            );
        }
    }

    protected function processGPSByGpsIdEupfin($data)
    {
        foreach ($data as $vehicle) {
            app('App\Repositories\VehicleRepository')->updateGpsVehicleByVehiclePlate(
                $vehicle->VehicleNo, $vehicle->Latitude, $vehicle->Longitude, $vehicle->Address
            );
        }
    }

    protected function processGPSByGpsIdADA($data)
    {
        foreach ($data as $vehicle) {
            app('App\Repositories\VehicleRepository')->updateGpsVehicleByGpsId(
                $vehicle->vehicle_id, $vehicle->latitude, $vehicle->longitude, $vehicle->address
            );
        }
    }

    protected function processGPSByGpsId($data)
    {
        foreach ($data as $vehicle) {
            app('App\Repositories\VehicleRepository')->updateGpsVehicleByGpsId(
                $vehicle->Id, $vehicle->Y, $vehicle->X, $vehicle->Address
            );
        }
    }

    protected function processGPS($data)
    {
        try {
            foreach ($data as $vehicle) {
                app('App\Repositories\VehicleRepository')->updateGpsVehicle(
                    $vehicle->VehiclePlate, $vehicle->Latitude, $vehicle->Longitude, $vehicle->Address
                );
            }
        } catch (\Exception $exception) {
        }
    }

    protected function processGPSVComSat($data)
    {
        try {
            foreach ($data as $vehicle) {
                app('App\Repositories\VehicleRepository')->updateGpsVehicle(
                    str_replace(array("-", " ", "."), "", $vehicle->CarPlate), $vehicle->Lat, $vehicle->Lng, $vehicle->Address
                );
            }
        } catch (\Exception $exception) {
        }
    }

    protected function doReportSchedule()
    {
        try {
            $request = new Request();
            app('App\Http\Controllers\Api\ReportScheduleController')->doReportSchedule($request);
        } catch (\Exception $exception) {
            logError('********************* Exception Kernel:' . $exception->getMessage(), []);
        }
    }

    /**
     *  Gửi email thông kê số lượng xe hết hạn giấy phép ngày và xe đến hạn bảo dưỡng
     */
    protected function doWarningVehicleSchedule()
    {
        try {
            app('App\Http\Controllers\Api\ReportScheduleController')->vehicleWarningScheduleEmail();
        } catch (\Exception $exception) {
            logError('********************* Exception Kernel:' . $exception->getMessage(), []);
        }
    }

    protected function doProcessDataUpload()
    {
        try {
            $request = new Request();
            app('App\Http\Controllers\Api\ReportScheduleController')->processDataUpload($request);
        } catch (\Exception $exception) {
            logError('********************* Exception Kernel:' . $exception->getMessage(), []);
        }
    }

    protected function doUpdateStatusVehicle()
    {
        try {
            $request = new Request();
            app('App\Http\Controllers\Api\RouteApiController')->updateVehicleStatus($request);
        } catch (\Exception $exception) {
            logError('********************* Exception Kernel:' . $exception->getMessage(), []);
        }
    }

    protected function doDailyReportByVehicle()
    {
        $enableGps = env('GPS_STATUS', false);
        if ($enableGps) {
            try {
                $request = new Request();
                app('App\Http\Controllers\Api\RouteApiController')->doDailyReportsAll($request);
            } catch (\Exception $exception) {
                logError('********************* Exception doDailyReportByVehicle:' . $exception->getMessage(), []);
            }
        }
    }

    protected function doDailyDistanceReport()
    {
        try {
            $processDate = date('Y-m-d', strtotime("-1 days"));
            app('App\Http\Controllers\Api\RouteApiController')->scheduleDistanceReportDaily($processDate);
        } catch (\Exception $exception) {
            logError('********************* Exception Kernel:' . $exception->getMessage(), []);
        }
    }

    protected function doReportOperatorDaily()
    {
        try {
            app('App\Repositories\ReportDataRepository')->reportOperatorDaily();
        } catch (\Exception $exception) {
            logError('********************* Exception Kernel:' . $exception->getMessage(), []);
        }
    }

    protected function doCalcStatusDocumentsDaily()
    {
        try {
            app('App\Repositories\DocumentRepository')->calcStatusDocumentsDaily();
        } catch (\Exception $exception) {
            logError('********************* Exception Kernel:' . $exception->getMessage(), []);
        }
    }

    protected function doSyncOrderToPartner()
    {
        $tps = explode(',', env('TP_INTEGRATION_LIST'));
        try {
            foreach ($tps as $tpId) {
                if ($tpId == config('constant.3P_1MG')) {
                    app('App\Http\Controllers\Api\TPApiController')->updateOrderToPartner1MG();
                }
            }
        } catch (\Exception $exception) {
            logError('********************* Exception Kernel:' . $exception->getMessage(), []);
        }
    }

    protected function doLoginTP()
    {
        $tps = explode(',', env('TP_INTEGRATION_LIST'));
        try {
            foreach ($tps as $tpId) {
                if ($tpId == config('constant.3P_1MG')) {
                    app('App\Http\Controllers\Api\TPApiController')->loginToken1MG();
                }
            }
        } catch (\Exception $exception) {
            logError('********************* Exception doLoginTP:' . $exception->getMessage(), []);
        }
    }

    //Thông báo định kỳ đến tài xế
    protected function doPushNoticeDriver($hourType)
    {
        try {
            if (env("ENABLE_NOTIFY_DRIVER", 1) == 1)
                app('App\Http\Controllers\Api\NotificationApiController')->pushNoticeDriver($hourType);

        } catch (\Exception $exception) {
            logError('********************* Exception doPushNoticeDriver:' . $exception->getMessage(), []);
        }
    }

    //Thông báo bảo dưỡng đến tài xế

    /**
     *
     */
    protected function doPushNoticeAccessory()
    {
        try {
            if (env("ENABLE_NOTIFY_ACCESSORY_DRIVER", 1) == 1)
                app('App\Http\Controllers\Api\NotificationApiController')->pushNoticeAccessory();

        } catch (\Exception $exception) {
            logError('********************* Exception doPushNoticeAccessoryDriver:' . $exception->getMessage(), []);
        }
    }

    protected function deleteVinhHienGPSData()
    {
        $enableGps = env('GPS_STATUS', false);
        if ($enableGps) {
            $GCs = explode(',', env('GPS_COMPANY'));
            foreach ($GCs as $gc) {
                $gpsSyncLog = new GpsSyncLog();
                if ($gc == config('constant.GC_VINHHIEN')) {
                    app('App\Repositories\VinhHienGPSRepository')->deleteGpsData();
                }
            }
        }
    }

    //Xử lý event trong hàng đợi
    protected function doProcessEventInQueue()
    {
        try {
            AppConstant::listen();
        } catch (\Exception $exception) {
            logError('********************* Exception Kernel:' . $exception->getMessage(), []);
        }
    }
}
