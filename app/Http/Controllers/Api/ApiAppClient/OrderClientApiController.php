<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\AppConstant;
use App\Common\GoogleConstant;
use App\Common\HttpCode;
use App\Http\Controllers\Base\ClientApiController;
use App\Model\Entities\OrderCustomer;
use App\Repositories\Client\OrderClientClientRepository;
use App\Repositories\CustomerRepository;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Input;
use Auth;
use Mockery\Exception;

class OrderClientApiController extends ClientApiController
{

    public function __construct(
        CustomerRepository $customerRepository,
        OrderClientClientRepository $repository
    ) {
        parent::__construct($customerRepository);
        $this->setRepository($repository);
    }

    public function saveFromClient(Request $request) {
        try {
            $userID = Auth::user()->id;
            $client = $this->getCustomerRepository()->getCustomerByUserId($userID);

            $customer = $this->getCustomerRepository()->getCustomerByParentId($client);
            $params = $request->all();
            $params['customer_id'] = $customer->id;

            if ($client->customer_type == 3) {
                $params['client_id'] = $client->id;
            }

            $isValidate = $this->getRepository()->getValidator()->validateClientApi($params);
            if (!$isValidate) {
                $errors = $this->getRepository()->getValidator()->errorsBag();
                $validators = [];
                foreach ($errors->messages() as $key => $message) {
                    $validators[] = [
                        'fieldName' => $key,
                        'errorMessage' => Arr::get($message, 0)
                    ];
                }
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $validators,
                    'data' => null
                ]);
            }
            $entity = $this->getRepository()->saveEntity($userID, $params);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $entity
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]
            );
        }
    }

    //Lấy danh sách thông tin chi tiết đơn hàng cho client
    public function order(Request $request)
    {
        $orders = $this->getRepository()->getOrdersByID(Request::get('id'));
        return response()->json([
            'errorCode' => HttpCode::EC_OK,
            'errorMessage' => '',
            'data' => $orders
        ]);
    }

    //Lấy danh sách đơn hàng của khách hàng
    public function event()
    {
        $start = Request::get('start');
        $end = Request::get('end');

        $userId = Auth::User()->id;
        $customer = $this->getCustomerRepository()->getCustomerByUserId($userId);
        $events = $this->getRepository()->getEventByCustomerID($start, $end, $customer->id);
        return json_encode($events);
    }
}
