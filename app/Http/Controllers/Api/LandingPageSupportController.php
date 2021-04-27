<?php

namespace App\Http\Controllers\Api;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Model\Entities\LpCompanySupport;
use Illuminate\Http\Request;
use JWTAuth;
use Mockery\Exception;
use Validator;

class LandingPageSupportController extends ApiController
{
    protected $lpCompanySupportRepo;

    public function getLpCompanySupportRepo()
    {
        return $this->lpCompanySupportRepo;
    }

    public function setLpCompanySupportRepo($lpCompanySupportRepo)
    {
        $this->lpCompanySupportRepo = $lpCompanySupportRepo;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function signUpCompany(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'companyName' => 'required',
                'fullName' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'type' => '',
                'remark' => ''
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $company = new LpCompanySupport();
                $company->company_name = $request['companyName'];
                $company->full_name = $request['fullName'];
                $company->phone = $request['phone'];
                $company->email = $request['email'];
                $company->type = $request['type'];
                $company->remark = $request['remark'];
                $company->save();

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => [
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