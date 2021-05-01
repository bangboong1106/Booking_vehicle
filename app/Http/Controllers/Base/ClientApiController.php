<?php

namespace App\Http\Controllers\Base;

use App\Common\HttpCode;
use App\Helpers\Url;
use App\Model\Entities\Customer;
use App\Repositories\CustomerRepository;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Mockery\Exception;
use DB;
use Input;
use JWTAuth;
use Validator;

/**
 * Class ApiController
 * @package App\Http\Controllers\Base
 */
class ClientApiController extends ApiController
{

    protected $customerRepository;

    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }

    public function setCustomerRepository($customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * ApiController constructor.
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        parent::__construct();
        $this->setCustomerRepository($customerRepository);
    }

    public function getCurrentUser()
    {
        return apiGuard()->user();
    }

    // API lấy danh sách đối tượng
    // CreatedBy nlhoang 29/06/2020
    public function list(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'pageSize' => 'required',
                'pageIndex' => 'required',
                'textSearch' => '',
                'sort' => [
                    'sortField' => '',
                    'sortType' => ''
                ]
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                // $userId = Auth::User()->id;

                // $client = $this->getCustomerRepository()->getCustomerByUserId($userId);
                // $clientID = $client->id;

                // $customer = $this->getCustomerRepository()->getCustomerByParentId($client);

                $items = $this->_repository->getDataList(null, null, $request);
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => $items
                ]);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    // API lấy chi tiết bản ghi
    // CreatedBy nlhoang 20/05/2020
    public function detail(Request $request)
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
            }
            $id = $request->get('id', 0);
            $userId = Auth::User()->id;

            $client = $this->getCustomerRepository()->getCustomerByUserId($userId);

            $customer = $this->getCustomerRepository()->getCustomerByParentId($client);

            $customerDetail = $this->getRepository()->getDataForClientByID($customer->id, $id);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $customerDetail
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

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function delete(Request $request)
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
            }
            $id = $request->get('id', 0);

            if (is_string($id)) {
                $id = explode(',', $id);
            }
            
            $this->getRepository()->deleteDataByID($id);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => null
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

    // API lưu thông tin
    // CreatedBy nlhoang 29/06/2020
    public function save(Request $request)
    {
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

    // API lấy lịch sử sửa đổi của bản ghi
    // CreatedBy nlhoang 29/06/2020
    public function auditing(Request $request)
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
            }
            $id = $request->get('id', 0);
            $data = $this->getRepository()->getAuditing($id);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
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
}
