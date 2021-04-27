<?php

namespace App\Http\Controllers\Api;

use App\Common\AppConstant;
use App\Http\Controllers\Base\ApiController;
use App\Model\Entities\GpsSyncLog;
use App\Repositories\TPActionSyncRepository;
use App\Repositories\TPApiConfigRepository;
use App\Repositories\TPApiInfoRepository;
use Validator;

class TPApiController extends ApiController
{
    protected $tpApiRepos;
    protected $tpApiInfoRepos;
    protected $tpActionSyncRepos;

    public function getTpApiRepos()
    {
        return $this->tpApiRepos;
    }

    public function setTpApiRepos($tpApiRepos)
    {
        $this->tpApiRepos = $tpApiRepos;
    }

    public function getTPApiInfoRepos()
    {
        return $this->tpApiInfoRepos;
    }

    public function setTPApiInfoRepos($tpApiInfoRepos)
    {
        $this->tpApiInfoRepos = $tpApiInfoRepos;
    }

    public function getTPActionSyncRepos()
    {
        return $this->tpActionSyncRepos;
    }

    public function setTPActionSyncRepos($tpActionSyncRepos)
    {
        $this->tpActionSyncRepos = $tpActionSyncRepos;
    }

    public function __construct(TPApiConfigRepository $TPApiConfigRepository, TPApiInfoRepository $apiInfoRepository
        , TPActionSyncRepository $actionSyncRepository)
    {
        parent::__construct();
        $this->setTpApiRepos($TPApiConfigRepository);
        $this->setTPApiInfoRepos($apiInfoRepository);
        $this->setTPActionSyncRepos($actionSyncRepository);
    }


    // Start TP: 1MG
    public function loginToken1MG()
    {
        $apiConfig = $this->getTpApiRepos()->search([
            'id', '=', config('constant.3P_1MG')
        ])->first();

        if (isset($apiConfig)) {
            $gpsSyncLog = new GpsSyncLog();
            try {
                $client = new \GuzzleHttp\Client();
                $request = $client->post(env('1MG_LOGIN_URL', 'https://api-commerce-sandbox.vinid.dev/authorizationserver/oauth/token'), ['form_params' =>
                    [
                        'client_id' => $apiConfig->client_id,
                        'client_secret' => $apiConfig->client_secret,
                        'grant_type' => $apiConfig->grant_type,
                        'username' => $apiConfig->username,
                        'password' => $apiConfig->password,
                    ]
                ]);
                $response = $request->getBody();
                $gpsSyncLog->request = json_encode(['request' => env('1MG_LOGIN_URL', 'https://api-commerce-sandbox.vinid.dev/authorizationserver/oauth/token')]);

                if ($response != null) {
                    $content = $response->getContents();
                    $gpsSyncLog->response = $content;
                    $data = json_decode($content);
                    if (!empty($data)) {
                        $apiConfig->access_token = $data->token_type . ' ' . $data->access_token;
                        $apiConfig->token_type = $data->token_type;
                        $apiConfig->refresh_token = $data->refresh_token;
                        $apiConfig->expires_in = $data->expires_in;
                        $apiConfig->scope = $data->scope;
                        $apiConfig->note = $data->uuid;
                        $apiConfig->save();
                    }
                }
            } catch (\Exception $exception) {
                $gpsSyncLog->error_code = 'Exception';
                $gpsSyncLog->error_message = $exception->getMessage();
            }
            $gpsSyncLog->type_request = 'Login 1MG';
            $gpsSyncLog->save();
        }
    }

    public function refreshToken1MG()
    {
        $apiConfig = $this->getTpApiRepos()->search([
            'id', '=', config('constant.3P_1MG')
        ])->first();

        if (isset($apiConfig)) {
//            $gpsSyncLog = new GpsSyncLog();
//            try {
//                $client = new \GuzzleHttp\Client();
//                $request = $client->post('https://api-commerce-sandbox.vinid.dev/authorizationserver/oauth/token', ['body' => json_encode(
//                    [
//                        'client_id' => $apiConfig->client_id,
//                        'client_secret' => $apiConfig->client_secret,
//                        'grant_type' => $apiConfig->grant_type,
//                        'username' => $apiConfig->username,
//                        'password' => $apiConfig->password,
//                    ]
//                )]);
//                $response = $request->getBody();
//                $gpsSyncLog->request = json_encode(['request' => 'https://api-commerce-sandbox.vinid.dev/authorizationserver/oauth/token']);
//
//                if ($response != null) {
//                    $content = $response->getContents();
//                    $gpsSyncLog->response = $content;
//                    $data = json_decode($content);
//                    if (!empty($data)) {
//                        $apiConfig->access_token = $data->access_token;
//                        $apiConfig->token_type = $data->token_type;
//                        $apiConfig->refresh_token = $data->refresh_token;
//                        $apiConfig->expires_in = $data->expires_in;
//                        $apiConfig->scope = $data->scope;
//                        $apiConfig->note = $data->uuid;
//                        $apiConfig->save();
//                    }
//                }
//            } catch (\Exception $exception) {
//                $gpsSyncLog->error_code = 'Exception';
//                $gpsSyncLog->error_message = $exception->getMessage();
//            }
//            $gpsSyncLog->type_request = 'Login 1MG';
//            $gpsSyncLog->save();
        }
    }

    public function printBill1MG()
    {
        $apiConfig = $this->getTpApiRepos()->search([
            'id', '=', config('constant.3P_1MG')
        ])->first();

        if (isset($apiConfig)) {
            $gpsSyncLog = new GpsSyncLog();
            $testShipmentId = '005110700001';
            try {
                $client = new \GuzzleHttp\Client(['headers' => [$apiConfig->request_header_authen => $apiConfig->access_token]]);
                $request = $client->get(env('1MG_PRINT_URL','https://api-commerce-sandbox.vinid.dev/partner/v2/3pls/print/') . $testShipmentId);
                $response = $request->getBody();
                $gpsSyncLog->request = json_encode(['request' => env('1MG_PRINT_URL','https://api-commerce-sandbox.vinid.dev/partner/v2/3pls/print/') . $testShipmentId]);

                if ($response != null) {
                    $content = $response->getContents();
                    // TODO...
                }
            } catch (\Exception $exception) {
                $gpsSyncLog->error_code = 'Exception';
                $gpsSyncLog->error_message = $exception->getMessage();
            }
            $gpsSyncLog->type_request = 'Login 1MG';
            $gpsSyncLog->save();
        } else {
            // TODO...
        }
    }

    public function updateOrderToPartner1MG()
    {
        try {
            //Lay thong tin api
            $apiInfoList = $this->getTPApiInfoRepos()->getApiInfoList(config('constant.UPDATE_ORDER'))->pluck('url', 'partner_name');

            if ($apiInfoList != null) {
                //Call api 1MG
                if ($apiInfoList[config('constant.1MG_NAME')]) {
                    //Lay du lieu cap nhat
                    $actionSyncList = $this->getTPActionSyncRepos()->getActionNotSyncByPartner(config('constant.1MG_NAME'));
                    if ($actionSyncList) {
                        $apiConfig = $this->getTpApiRepos()->getApiConfig(config('constant.1MG_NAME'));
                        $datas = [];
                        $sendList = [];
                        foreach ($actionSyncList as $actionSync) {
                            if (!array_key_exists($actionSync->order_id, $datas)) {
                                $datas[$actionSync->order_id] = [
                                    "order_no" => $actionSync->order_no,
                                    "bill_no" => $actionSync->bill_no,
                                    "status" => $actionSync->status,
                                    "etd_reality" => $actionSync->ETD_date_reality ? AppConstant::convertDate($actionSync->ETD_date_reality, 'Y-m-d')
                                        . ' ' . AppConstant::convertTime($actionSync->ETD_time_reality, 'H:i') : '',
                                    "eta_reality" => $actionSync->ETA_date_reality ? AppConstant::convertDate($actionSync->ETA_date_reality, 'Y-m-d')
                                        . ' ' . AppConstant::convertTime($actionSync->ETA_time_reality, 'H:i') : '',
                                    "note" => base64_encode($actionSync->note ? $actionSync->note : ""),
                                    "amount" => $actionSync->amount
                                ];
                                $sendList[] = $actionSync->id;
                            }
                        }
                        if ($datas && count($datas) > 0) {
                            foreach ($datas as $orderId => $order) {
                                $gpsSyncLog = new GpsSyncLog();

                                $client = new \GuzzleHttp\Client(['headers' => [
                                    'Authorization' => $apiConfig->access_token
                                ]]);

                                $request = $client->post($apiInfoList[config('constant.1MG_NAME')], ['body' => json_encode($order)]);

                                $logRequest = json_encode(['request' => $apiInfoList[config('constant.1MG_NAME')]
                                    , 'headers' => ['Authorization' => $apiConfig->access_token], 'body' => $order]);
                                $gpsSyncLog->request = $logRequest;

                                $response = $request->getBody();
                                if ($response != null) {

                                    $gpsSyncLog->response = $response;
                                    logError($response);

                                    $data = json_decode($response);
                                    if (!empty($data) && $data->code == 200) {
                                        //Cap nhat thanh cong
                                        foreach ($actionSyncList as $actionSync) {
                                            if ($actionSync->order_id == $orderId) {
                                                $actionSync->sended = 99;
                                                if (in_array($actionSync->id, $sendList)) {
                                                    $actionSync->request_description = $logRequest;
                                                    $actionSync->response_code = $data->code;
                                                    $actionSync->response_description = $response;
                                                }
                                                $actionSync->save();
                                            }
                                        }
                                    } else {
                                        //That bai: tang lan gui len 1
                                        foreach ($actionSyncList as $actionSync) {
                                            if ($actionSync->order_id == $orderId) {
                                                $actionSync->sended = $actionSync->sended + 1;
                                                if (in_array($actionSync->id, $sendList)) {
                                                    $actionSync->request_description = $logRequest;
                                                    $actionSync->response_code = $data->code;
                                                    $actionSync->response_description = $response;
                                                }
                                                $actionSync->save();
                                            }
                                        }
                                    }
                                }
                                $gpsSyncLog->type_request = 'Update Order 1MG';
                                $gpsSyncLog->save();
                            }
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            logError($e);
        }
    }
    // End TP: 1MG
}