<?php

namespace App\Http\Controllers\Api\ApiAppManagement;

use App\Common\HttpCode;
use App\Http\Controllers\Base\ManagementApiController;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\LocationRepository;
use App\Repositories\Management\OrderManagementRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderFileRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\RouteCostRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\TPActionSyncRepository;
use App\Repositories\VehicleRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Input;
use JWTAuth;
use Exception;
use Validator;
use App\Common\AppConstant;

class DocumentApiController extends ManagementApiController
{

    protected $orderRepos;
    protected $fileRepos;
    protected $orderFileRepos;
    protected $driverRepos;
    protected $orderHistoryRepos;
    protected $customerRepos;
    protected $vehicleRepos;
    protected $routeRepos;
    protected $routeCostRepos;
    protected $orderCustomerRepos;
    protected $locationRepos;
    protected $tpActionSyncRepos;

    /**
     * @return OrderRepository
     */
    public function getOrderRepos()
    {
        return $this->orderRepos;
    }

    public function setOrderRepos($orderRepos)
    {
        $this->orderRepos = $orderRepos;
    }

    public function getFileRepos()
    {
        return $this->fileRepos;
    }

    public function setFileRepos($fileRepos)
    {
        $this->fileRepos = $fileRepos;
    }

    public function getOrderFileRepos()
    {
        return $this->orderFileRepos;
    }

    public function setOrderFileRepos($orderFileRepos)
    {
        $this->orderFileRepos = $orderFileRepos;
    }

    public function getDriverRepos()
    {
        return $this->driverRepos;
    }

    public function setDriverRepos($driverRepos)
    {
        $this->driverRepos = $driverRepos;
    }

    public function getOrderHistoryRepos()
    {
        return $this->orderHistoryRepos;
    }

    public function setOrderHistoryRepos($orderHistoryRepos)
    {
        $this->orderHistoryRepos = $orderHistoryRepos;
    }

    public function getCustomerRepos()
    {
        return $this->customerRepos;
    }

    public function setCustomerRepos($customerRepos)
    {
        $this->customerRepos = $customerRepos;
    }

    public function getVehicleRepos()
    {
        return $this->vehicleRepos;
    }

    public function setVehicleRepos($vehicleRepos)
    {
        $this->vehicleRepos = $vehicleRepos;
    }

    public function getRouteRepos()
    {
        return $this->routeRepos;
    }

    public function setRouteRepos($routeRepos)
    {
        $this->routeRepos = $routeRepos;
    }

    public function getRouteCostRepos()
    {
        return $this->routeCostRepos;
    }

    public function setRouteCostRepos($routeCostRepos)
    {
        $this->routeCostRepos = $routeCostRepos;
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

    /**
     * @return TPActionSyncRepository
     */
    public function getTPActionSyncRepos()
    {
        return $this->tpActionSyncRepos;
    }

    /**
     * @param mixed $tpActionSyncRepository
     */
    public function setTPActionSyncRepos($tpActionSyncRepository)
    {
        $this->tpActionSyncRepos = $tpActionSyncRepository;
    }

    public function __construct(
        OrderManagementRepository $orderRepository,
        FileRepository $fileRepository,
        OrderFileRepository $orderFileRepository,
        DriverRepository $driverRepository,
        OrderHistoryRepository $orderHistoryRepository,
        CustomerRepository $customerRepository,
        VehicleRepository $vehicleRepos,
        RoutesRepository $routesRepository,
        RouteCostRepository $routeCostRepos,
        OrderCustomerRepository $orderCustomerRepository,
        LocationRepository $locationRepository,
        TPActionSyncRepository $tpActionSyncRepository
    ) {
        parent::__construct();
        $this->setRepository($orderRepository);
        $this->setFileRepos($fileRepository);
        $this->setOrderFileRepos($orderFileRepository);
        $this->setDriverRepos($driverRepository);
        $this->setOrderHistoryRepos($orderHistoryRepository);
        $this->setCustomerRepos($customerRepository);
        $this->setVehicleRepos($vehicleRepos);
        $this->setRouteRepos($routesRepository);
        $this->setRouteCostRepos($routeCostRepos);
        $this->setOrderCustomerRepos($orderCustomerRepository);
        $this->setLocationRepos($locationRepository);
        $this->setTPActionSyncRepos($tpActionSyncRepository);
    }

    /**
     * @param Request $data
     * Cập nhật thông tin chứng từ
     * Created by ptly on 2020.06.23
     */
    public function updateOrderDocument(Request $data)
    {
        try {
            $entity = $this->getRepository()->getOrderById($data['id']);
            if ($entity != null) {
                $entity->is_collected_documents = $data['is_collected_documents'];
                $entity->status_collected_documents = $data['status_collected_documents'];
                if (!empty($data['date_collected_documents']))
                    $entity->date_collected_documents = AppConstant::convertDate($data['date_collected_documents'], 'Y-m-d');
                else
                    $entity->date_collected_documents = null;
                $entity->time_collected_documents = $data['time_collected_documents'];
                $entity->time_collected_documents_reality = $data['time_collected_documents_reality'];
                if (!empty($data['date_collected_documents_reality']))
                    $entity->date_collected_documents_reality = AppConstant::convertDate($data['date_collected_documents_reality'], 'Y-m-d');
                else
                    $entity->date_collected_documents_reality = null;
                $entity->num_of_document_page = $data['num_of_document_page'];
                $entity->document_type = $data['document_type'];
                $entity->document_note = $data['document_note'];

                $entity = $this->calcStatusDocument($entity);
                $entity->save();
                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                    'data' => ''
                ]);
            } else {
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function calcStatusDocument($order)
    {
        if ($order->status_collected_documents != config("constant.DA_THU_DU") && !empty($order->date_collected_documents)) {
            if (time() - strtotime($order->date_collected_documents . ' ' . $order->time_collected_documents) > 0) {
                $order->status_collected_documents = config("constant.QUA_HAN");
                return $order;
            }
            if (date('Y-m-d', strtotime(' today')) == date('Y-m-d', strtotime($order->date_collected_documents))) {
                $order->status_collected_documents = config("constant.DEN_HAN_VAO_HOM_NAY");
                return $order;
            }
            if (date('Y-m-d', strtotime(' +1 day')) == date('Y-m-d', strtotime($order->date_collected_documents))) {
                $order->status_collected_documents = config("constant.DEN_HAN_VAO_HOM_SAU");
                return $order;
            }
        }
        return $order;
    }
}
