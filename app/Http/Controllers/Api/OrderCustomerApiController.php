<?php

namespace App\Http\Controllers\Api;

use App\Common\AppConstant;
use App\Common\GoogleConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Model\Entities\OrderCustomer;
use App\Repositories\CustomerRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderCustomerRepository;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Input;
use JWTAuth;
use Mockery\Exception;
use Swift_Mailer;
use Swift_SmtpTransport;
use Validator;

class OrderCustomerApiController extends ApiController
{

    protected $customerRepos;
    protected $orderCustomerRepos;
    protected $locationRepos;
    protected $districtRepos;

    public function getCustomerRepos()
    {
        return $this->customerRepos;
    }

    public function setCustomerRepos($customerRepos)
    {
        $this->customerRepos = $customerRepos;
    }

    public function getOrderCustomerRepos()
    {
        return $this->orderCustomerRepos;
    }

    public function setOrderCustomerRepos($orderCustomerRepos)
    {
        $this->orderCustomerRepos = $orderCustomerRepos;
    }

    public function getLocationRepos()
    {
        return $this->locationRepos;
    }

    public function setLocationRepos($locationRepos)
    {
        $this->locationRepos = $locationRepos;
    }

    public function getDistrictRepos()
    {
        return $this->districtRepos;
    }

    public function setDistrictRepos($districtRepos)
    {
        $this->districtRepos = $districtRepos;
    }


    public function __construct(CustomerRepository $customerRepository, OrderCustomerRepository $orderCustomerRepository,
                                LocationRepository $locationRepository,
                                DistrictRepository $districtRepository)
    {
        parent::__construct();

        $this->setCustomerRepos($customerRepository);
        $this->setOrderCustomerRepos($orderCustomerRepository);
        $this->setLocationRepos($locationRepository);
        $this->setDistrictRepos($districtRepository);
    }

    public function storeOrderCustomer(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'customer_name' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }

            DB::beginTransaction();

            $orderCustomer = new OrderCustomer();
            $orderCustomer->source_creation = config('constant.FROM_CLIENT');
            $systemCode = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order_customer'), null, true);
            $orderCustomer->code = $systemCode;
            $orderCustomer->name = $systemCode;
            $currentDay = new DateTime();
            $orderCustomer->order_date = $currentDay->format('Y-m-d');
            $orderCustomer->ins_id = 0;

            //Tao moi KH
            $customer = $this->getCustomerRepos()->findByFullName($request['customer_name']);
            if (!$customer || $request['customer_mobile_no'] != $customer->mobile_no) {
                $customer = $this->getCustomerRepos()->findFirstOrNew([]);
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_customer'), null, true);
                $customer->customer_code = $code;
                $customer->active = 1;
                $customer->type = config('constant.INDIVIDUAL_CUSTOMERS');
                $customer->full_name = $request['customer_name'];
                $customer->mobile_no = $request['customer_mobile_no'];
                $customer->save();
            }

            $orderCustomer->customer_id = $customer->id;
            $orderCustomer->customer_name = $request['customer_name'];
            $orderCustomer->customer_mobile_no = $request['customer_mobile_no'];
            $orderCustomer->customer_email = $request['email'];

            //Them moi dia diem
            $locationDes = $this->doLocation($request['ETD_location']['province'], $request['ETD_location']['district']
                , $request['ETD_location']['address'], $request['ETD_location']['full_address']);
            if ($locationDes) {
                $orderCustomer->location_destination_id = $locationDes->id;
            }
            $locationArr = $this->doLocation($request['ETA_location']['province'], $request['ETA_location']['district']
                , $request['ETA_location']['address'], $request['ETA_location']['full_address']);
            if ($locationArr) {
                $orderCustomer->location_arrival_id = $locationArr->id;
            }

            $orderCustomer->ETD_date = empty($request['ETD_date']) ? null : AppConstant::convertDate($request['ETD_date'], 'Y-m-d');
            $orderCustomer->ETD_time = $request['ETD_time'];
            $orderCustomer->ETA_date = empty($request['ETA_date']) ? null : AppConstant::convertDate($request['ETA_date'], 'Y-m-d');
            $orderCustomer->ETA_time = $request['ETA_time'];

            $orderCustomer->distance = $request['distance'];
            $orderCustomer->weight = $request['weight'];
            $orderCustomer->volume = $request['volume'];

            $orderCustomer->save();

            //Luu chung loai xe
            if ($request['listVehicleGroup']) {
                $data = [];
                foreach ($request['listVehicleGroup'] as $item) {
                    $data[] = [
                        'order_customer_id' => $orderCustomer->id,
                        'vehicle_group_id' => !empty($item['VehicleGroupID']) ? $item['VehicleGroupID'] : 0,
                        'vehicle_number' => !empty($item['RouteNumber']) ? $item['RouteNumber'] : 0
                    ];
                }
                $orderCustomer->listVehicleGroups()->detach();
                $orderCustomer->listVehicleGroups()->sync($data);
            }

            DB::commit();

            $this->doSendEmailCreateOrderSuccess($request, $systemCode, $currentDay);

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => [
                    'message' => 'ok'
                ]
            ]);

        } catch (Exception $exception) {
            logError($exception);
            DB::rollBack();
            return response()->json(['errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)]);
        }
    }

    public function doLocation($province, $district, $address, $full_address)
    {

        $locationEntity = $this->getLocationRepos()->findByFullAddress($full_address);
        if ($locationEntity)
            return $locationEntity;

        $locationEntity = $this->getLocationRepos()->findFirstOrNew([]);
        $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_location'), null, true);
        $locationEntity->code = $code;
        $locationEntity->title = $full_address;
        $locationEntity->full_address = $full_address;
        $locationEntity->address = $address;
        $locationEntity->province_id = $province;
        $locationEntity->district_id = $district;
        $locationEntity->address_auto_code = $province . "-" . $district;
        if ($district) {
            $districtEntity = $this->getDistrictRepos()->findFirstOrNewByDistrictId(["district_id" => $district]);
            if ($districtEntity) {
                $coordinate = $districtEntity->location;
                //Convert tọa độ sang long
                $googleConstant = new GoogleConstant(env('GOOGLE_MAP_API_KEY', ''));
                $latLong = $googleConstant->convertDMSToLatLong($coordinate);

                $latitude = !empty($latLong) ? $latLong['latitude'] : '';
                $longitude = !empty($latLong) ? $latLong['longitude'] : '';

                $locationEntity->latitude = $latitude;
                $locationEntity->longitude = $longitude;
            }
        }
        $locationEntity->save();
        return $locationEntity;
    }

    public function doSendEmailCreateOrderSuccess($request, $orderCode, $currentDay)
    {
        if ($request && $request['email']) {
            $data = array(
                'date' => $currentDay,
                'request' => $request,
                'orderCode' => $orderCode,
                'url' => request()->url()
            );
            $emailTitle = 'Đơn hàng ' . $orderCode . ' đã được xử lý thành công.';
            $emails = $request['email'];

            $backup = Mail::getSwiftMailer();

            $transport = new  Swift_SmtpTransport(env('MAIL_HOST_CLIENT'), env('MAIL_PORT_CLIENT'), env('MAIL_ENCRYPTION_CLIENT'));
            $transport->setUsername(env('MAIL_USERNAME_CLIENT'));
            $transport->setPassword(env('MAIL_PASSWORD_CLIENT'));

            $gmail = new Swift_Mailer($transport);

            Mail::setSwiftMailer($gmail);

            Mail::send('layouts.backend.elements.email.create_order_client_success_mail', $data, function ($message) use ($emailTitle, $emails) {
                $message->to($emails)->subject($emailTitle);
                $message->from(env('MAIL_USERNAME_CLIENT'), 'Thông báo');
            });

            Mail::setSwiftMailer($backup);
        }

    }
}