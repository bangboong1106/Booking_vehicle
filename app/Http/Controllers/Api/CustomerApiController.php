<?php

namespace App\Http\Controllers\Api;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Repositories\CustomerRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\WardRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Input;
use JWTAuth;
use Mockery\Exception;
use Validator;

class CustomerApiController extends ApiController
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
        WardRepository $wardRepository
    ) {
        parent::__construct();
        $this->setCustomerRepos($customerRepository);
        $this->setProvinceRepos($provinceRepository);
        $this->setDistrictRepos($districtRepos);
        $this->setWardRepos($wardRepository);
    }

    public function getProfile()
    {
        $entity = $this->getCustomerRepos()->getCustomerTypeByUserId(Auth::user()->id);
        if ($entity)
            return response()->json($entity);
        return response()->json([]);
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
                ]
            );
        }
    }
}
