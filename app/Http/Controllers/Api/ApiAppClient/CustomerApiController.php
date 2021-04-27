<?php

namespace App\Http\Controllers\Api\ApiAppClient;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ClientApiController;
use App\Repositories\Client\CustomerClientRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\WardRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Input;
use JWTAuth;
use Exception;
use Validator;

class CustomerApiController extends ClientApiController
{

    protected $customerRepos;
    protected $provinceRepos;
    protected $districtRepos;
    protected $wardRepos;

    public function getCustomerRepos()
    {
        return $this->customerRepos;
    }

    public function setCustomerRepos($customerRepos)
    {
        $this->customerRepos = $customerRepos;
    }

    public function getDistrictRepos()
    {
        return $this->districtRepos;
    }

    public function setDistrictRepos($districtRepos)
    {
        $this->districtRepos = $districtRepos;
    }

    public function getWardRepos()
    {
        return $this->wardRepos;
    }

    public function setWardRepos($wardRepos)
    {
        $this->wardRepos = $wardRepos;
    }

    public function getProvinceRepos()
    {
        return $this->provinceRepos;
    }

    public function setProvinceRepos($provinceRepos)
    {
        $this->provinceRepos = $provinceRepos;
    }

    public function __construct(
        CustomerRepository $customerRepository,
        ProvinceRepository $provinceRepository,
        DistrictRepository $districtRepos,
        WardRepository $wardRepository,
        CustomerClientRepository $customerClientRepository
    )
    {
        parent::__construct($customerRepository);
        $this->setCustomerRepos($customerRepository);
        $this->setProvinceRepos($provinceRepository);
        $this->setDistrictRepos($districtRepos);
        $this->setWardRepos($wardRepository);
        $this->setRepository($customerClientRepository);
    }

    public function getProfile()
    {
        $entity = $this->getCustomerRepos()->getCustomerTypeByUserId(Auth::user()->id);
        if ($entity)
            return response()->json($entity);
        return response()->json([]);
    }

    public function userinfo()
    {
        $userId = Auth::User()->id;
        $customer = $this->getCustomerRepos()->getCustomerByUserId($userId);
        $response = null;
        if ($customer) {
            $customerID = $customer->id;
            $response = $this->getCustomerRepos()->getCustomerInfo($customerID);
        }
        return json_encode($response);
    }

    public function updateField()
    {
        $params = Request::get('params');
        $userId = Auth::User()->id;
        $customer = $this->getCustomerRepos()->getCustomerTypeByUserId($userId);
        if ($customer) {
            $customerID = $customer->id;
            $this->getCustomerRepos()->updateField($params, $customerID);
        }
        return json_encode("");
    }

    public function getCustomerByCustomerID(Request $request)
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
            $customerID = $request->get('id', 0);
            $customerDetail = $this->getCustomerRepos()->getCustomerInfo($customerID);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $customerDetail
            ]);
        } catch (Exception $exception) {
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
                ]);
        }
    }

    public
    function updateProfile(Request $request)
    {
        $id = $request->get('id', 0);
        $full_name = $request->get('full_name', '');
        $email = $request->get('email', '');
        $mobile_no = $request->get('mobile_no', '');
        $delegate = $request->get('delegate', '');
        $tax_code = $request->get('tax_code', '');
        $current_address = $request->get('current_address', '');
        $province_id = $request->get('province_id', 0);
        $district_id = $request->get('district_id', 0);
        $ward_id = $request->get('ward_id', 0);
        $address = $request->get('address', '');
        $sex = $request->get('sex', null);
        $birth_date = $request->get('birth_date', null);

        $entity = $this->getCustomerRepos()->findFirstOrNew(['id' => $id]);
        $entity->full_name = $full_name;
        $entity->email = $email;
        $entity->mobile_no = $mobile_no;
        $entity->delegate = $delegate;
        $entity->full_name = $full_name;
        $entity->tax_code = $tax_code;
        $entity->current_address = $current_address;
        $entity->province_id = $province_id;
        $entity->district_id = $district_id;
        $entity->ward_id = $ward_id;
        $entity->address = $address;
        $entity->sex = $sex;
        $entity->birth_date = $birth_date;
        $entity->save();
    }
}
