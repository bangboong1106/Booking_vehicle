<?php

namespace App\Http\Controllers\Api;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ApiController;
use App\Repositories\DomainConfigRepository;
use App\Repositories\DriverRepository;
use App\Repositories\SystemConfigRepository;
use App\Repositories\VersionReviewRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use Validator;

class DomainApiController extends ApiController
{

    protected $domainRepos;
    protected $versionReviewRepos;
    protected $systemConfigRepository;
    protected $driverRepository;

    public function getDomainRepos()
    {
        return $this->domainRepos;
    }

    public function setDomainRepos($domainRepos)
    {
        $this->domainRepos = $domainRepos;
    }

    public function getVersionReviewRepos()
    {
        return $this->versionReviewRepos;
    }

    public function setVersionReviewRepos($versionReviewRepos)
    {
        $this->versionReviewRepos = $versionReviewRepos;
    }

    public function getSystemConfigRepository()
    {
        return $this->systemConfigRepository;
    }

    public function setSystemConfigRepository($systemConfigRepository)
    {
        $this->systemConfigRepository = $systemConfigRepository;
    }

    public function __construct(
        DomainConfigRepository $domainConfigRepository,
        VersionReviewRepository $versionReviewRepository,
        SystemConfigRepository $systemConfigRepository,
        DriverRepository $driverRepository
    ) {
        parent::__construct();
        $this->setDomainRepos($domainConfigRepository);
        $this->setVersionReviewRepos($versionReviewRepository);
        $this->setSystemConfigRepository($systemConfigRepository);
        $this->driverRepository = $driverRepository;
    }

    public function domainCode(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'code' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $domainConfig = $this->getDomainRepos()->getDomainConfig($request['code']);
                if (isset($domainConfig)) {
                    return response()->json([
                        'errorCode' => HttpCode::EC_OK,
                        'errorMessage' => '',
                        'data' => [
                            'code' => $domainConfig->code,
                            'domain' => $domainConfig->domain,
                            'description' => $domainConfig->description
                        ]
                    ]);
                } else {
                    return response()->json([
                        'errorCode' => HttpCode::EC_BAD_REQUEST,
                        'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_BAD_REQUEST)
                    ]);
                }
            }
        } catch (Exception $exception) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function versionReview(Request $request)
    {
        $version = $this->getVersionReviewRepos()->getCurrentVersionReview();
        return response()->json([
            'errorCode' => HttpCode::EC_OK,
            'errorMessage' => '',
            'data' => [
                'version' => isset($version) ? $version->version : '',
            ]
        ]);
    }

    public function configDriverApp(Request $request)
    {
        $configs = $this->getSystemConfigRepository()->search([
            'key_consf' => 'DriverMobile'
        ])->get();

        $driverMobileLimitedTime = $configs
            ->filter(function ($value) {
                return $value->key == "DriverMobile.CompletedLimitTime";
            });
        $allowCancelRoute = $configs
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowCancelRoute";
            });
        $allCancelOrder = $configs
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowCancelOrder";
            });
        $allowConfirmOrder = $configs
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowConfirmOrder";
            });
        $allowUploadRoute = $configs
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowUploadRoute";
            });
        $allowUploadOrder = $configs
            ->filter(function ($value) {
                return $value->key == "DriverMobile.AllowUploadOrder";
            });

        $ready_status = null;
        try {
            $userId = Auth::User()->id;
            if ($userId) {
                $driver = $this->driverRepository->getDriverByUserId($userId);
                if ($driver) {
                    $ready_status = $driver->ready_status;
                }
            }
        } catch (\Exception $exception) {
        }

        $data = [
            'support_car_transportation' => env("SUPPORT_CAR_TRANSPORTATION", false),
            'completed_limit_time' => $driverMobileLimitedTime->isEmpty() ? 0 : $driverMobileLimitedTime->first()->value,
            'allow_cancel_route' =>  $allowCancelRoute->isEmpty() ? 1 : $allowCancelRoute->first()->value,
            'allow_cancel_order' => $allCancelOrder->isEmpty() ? 1 : $allCancelOrder->first()->value,
            'allow_confirm_order' => $allowConfirmOrder->isEmpty() ? 1 : $allowConfirmOrder->first()->value,
            'allow_upload_route' => $allowUploadRoute->isEmpty() ? 1 : $allowUploadRoute->first()->value,
            'allow_upload_order' => $allowUploadOrder->isEmpty() ? 1 : $allowUploadOrder->first()->value,
            'ready_status' => $ready_status
        ];
        return response()->json([
            'errorCode' => HttpCode::EC_OK,
            'errorMessage' => '',
            'data' => $data
        ]);
    }
}
