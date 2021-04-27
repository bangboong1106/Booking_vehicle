<?php

namespace App\Http\Controllers\Backend;


use App\Common\HttpCode;
use App\Http\Controllers\Base\BackendController;

use App\Repositories\AdminUserInfoRepository;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderFileRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\QuotaRepository;
use App\Repositories\ReceiptPaymentRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\ExcelColumnConfigRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use stdClass;

class OrderEditorController extends BackendController
{
    protected $_customerRepository;
    protected $_locationRepository;
    protected $_orderHistoryRepository;
    protected $_vehicleRepository;
    protected $_fileRepository;
    protected $_orderFileRepository;
    protected $_driverRepository;
    protected $_contactRepository;
    protected $_columnConfigRepository;
    protected $_routesRepository;
    protected $_routeOrderRepository;
    protected $_quotaRepository;
    protected $_adminUserRepository;
    protected $_receiptPaymentRepository;
    protected $_orderCustomerRepository;
    protected $_orderPaymentRepos;
    protected $_excelColumnConfigRepository;

    /**
     * @return LocationRepository
     */
    public function getLocationRepository()
    {
        return $this->_locationRepository;
    }

    /**
     * @param mixed $locationRepository
     */
    public function setLocationRepository($locationRepository): void
    {
        $this->_locationRepository = $locationRepository;
    }

    /**
     * @return ProvinceRepository
     */
    public function getProvinceRepository()
    {
        return $this->_provinceRepository;
    }

    /**
     * @param mixed $provinceRepository
     */
    public function setProvinceRepository($provinceRepository): void
    {
        $this->_provinceRepository = $provinceRepository;
    }

    /**
     * @return CustomerRepository
     */
    public function getCustomerRepository()
    {
        return $this->_customerRepository;
    }

    /**
     * @param mixed $customerRepository
     */
    public function setCustomerRepository($customerRepository): void
    {
        $this->_customerRepository = $customerRepository;
    }

    /**
     * @return OrderHistoryRepository
     */
    public function getOrderHistoryRepository()
    {
        return $this->_orderHistoryRepository;
    }

    /**
     * @param mixed $orderHistoryRepository
     */
    public function setOrderHistoryRepository($orderHistoryRepository): void
    {
        $this->_orderHistoryRepository = $orderHistoryRepository;
    }

    /**
     * @return VehicleRepository
     */
    public function getVehicleRepository()
    {
        return $this->_vehicleRepository;
    }

    /**
     * @param mixed $vehicleRepository
     */
    public function setVehicleRepository($vehicleRepository): void
    {
        $this->_vehicleRepository = $vehicleRepository;
    }

    /**
     * @return FileRepository
     */
    public function getFileRepository()
    {
        return $this->_fileRepository;
    }

    /**
     * @param mixed $fileRepository
     */
    public function setFileRepository($fileRepository): void
    {
        $this->_fileRepository = $fileRepository;
    }

    /**
     * @return OrderFileRepository
     */
    public function getOrderFileRepository()
    {
        return $this->_orderFileRepository;
    }

    /**
     * @param mixed $orderFileRepository
     */
    public function setOrderFileRepository($orderFileRepository): void
    {
        $this->_orderFileRepository = $orderFileRepository;
    }

    /**
     * @return DriverRepository
     */
    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    /**
     * @param mixed $driverRepository
     */
    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }

    /**
     * @return ColumnConfigRepository
     */
    public function getColumnConfigRepository()
    {
        return $this->_columnConfigRepository;
    }

    /**
     * @param $columnConfigRepository
     */
    public function setColumnConfigRepository($columnConfigRepository): void
    {
        $this->_columnConfigRepository = $columnConfigRepository;
    }

    /**
     * @return RoutesRepository
     */
    public function getRoutesRepository()
    {
        return $this->_routesRepository;
    }

    /**
     * @param $routesRepository
     */
    public function setRoutesRepository($routesRepository): void
    {
        $this->_routesRepository = $routesRepository;
    }

    /**
     * @return mixed
     */
    public function getRouteOrderRepository()
    {
        return $this->_routeOrderRepository;
    }

    /**
     * @param $routeOrderRepository
     */
    public function setRouteOrderRepository($routeOrderRepository): void
    {
        $this->_routeOrderRepository = $routeOrderRepository;
    }

    /**
     * @return QuotaRepository
     */
    public function getQuotaRepository()
    {
        return $this->_quotaRepository;
    }

    /**
     * @param $quotaRepository
     */
    public function setQuotaRepository($quotaRepository): void
    {
        $this->_quotaRepository = $quotaRepository;
    }

    /**
     * @return mixed
     */
    public function getAdminUserRepository()
    {
        return $this->_adminUserRepository;
    }

    /**
     * @param mixed $adminUserRepository
     */
    public function setAdminUserRepository($adminUserRepository)
    {
        $this->_adminUserRepository = $adminUserRepository;
    }

    /**
     * @return mixed
     */
    public function getReceiptPaymentRepository()
    {
        return $this->_receiptPaymentRepository;
    }


    /**
     * @param mixed $receiptPaymentRepository
     */
    public function setReceiptPaymentRepository($receiptPaymentRepository)
    {
        $this->_receiptPaymentRepository = $receiptPaymentRepository;
    }

    /**
     * @return OrderCustomerRepository
     */
    public function getOrderCustomerRepository()
    {
        return $this->_orderCustomerRepository;
    }

    /**
     * @param mixed $orderCustomerRepository
     */
    public function setOrderCustomerRepository($orderCustomerRepository)
    {
        $this->_orderCustomerRepository = $orderCustomerRepository;
    }

    /**
     * @return mixed
     */
    public function getOrderPaymentRepos()
    {
        return $this->_orderPaymentRepos;
    }

    /**
     * @param mixed $orderPaymentRepos
     */
    public function setOrderPaymentRepos($orderPaymentRepos): void
    {
        $this->_orderPaymentRepos = $orderPaymentRepos;
    }

    /**
     * @return mixed
     */
    public function getExcelColumnConfigRepository()
    {
        return $this->_excelColumnConfigRepository;
    }

    /**
     * @param $excelColumnConfigRepository
     */
    public function setExcelColumnConfigRepository($excelColumnConfigRepository): void
    {
        $this->_excelColumnConfigRepository = $excelColumnConfigRepository;
    }

    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        LocationRepository $locationRepository,
        VehicleRepository $vehicleRepository,
        FileRepository $fileRepository,
        OrderFileRepository $orderFileRepository,
        DriverRepository $driverRepository,
        RoutesRepository $routesRepository,
        QuotaRepository $quotaRepository,
        AdminUserInfoRepository $adminUserInfoRepository,
        ReceiptPaymentRepository $receiptPaymentRepository,
        OrderCustomerRepository $orderCustomerRepository,
        OrderPaymentRepository $orderPaymentRepository,
        ExcelColumnConfigRepository $excelColumnConfigRepository
    )
    {
        parent::__construct();
        set_time_limit(0);
        $this->setRepository($orderRepository);

        $this->setVehicleRepository($vehicleRepository);
        $this->setCustomerRepository($customerRepository);
        $this->setLocationRepository($locationRepository);
        $this->setFileRepository($fileRepository);
        $this->setOrderFileRepository($orderFileRepository);
        $this->setDriverRepository($driverRepository);
        $this->setRoutesRepository($routesRepository);
        $this->setQuotaRepository($quotaRepository);
        $this->setAdminUserRepository($adminUserInfoRepository);
        $this->setReceiptPaymentRepository($receiptPaymentRepository);
        $this->setOrderCustomerRepository($orderCustomerRepository);
        $this->setOrderPaymentRepos($orderPaymentRepository);
        $this->setExcelColumnConfigRepository($excelColumnConfigRepository);
    }

    public function index()
    {
        parent::_cleanFormData();
        $this->detectCurrentPage();
        $this->_prepareIndex();

        $ids = Request::get("ids");
        $results = [];
        if ($ids) {
            $ids = explode(',', $ids);
            $withRelation = [
                'listLocationDestinations',
                'listLocationArrivals'
            ];
            $columns = [
                'orders.*',
                'lpd.title as name_of_province_destination_id',
                'lpa.title as name_of_province_arrival_id',
                'ldd.title as name_of_district_destination_id',
                'lda.title as name_of_district_arrival_id'
            ];
            $items = $this->getRepository()->getItemsByIds($ids, $columns, $withRelation);

            $results = [];
            foreach ($items as $item) {
                $item->ETD_time = isset($item->ETD_time) ? date('H:i', strtotime($item->ETD_time)) : null;
                $item->ETA_time = isset($item->ETA_time) ? date('H:i', strtotime($item->ETA_time)) : null;
                $item->ETD_time_reality = isset($item->ETD_time_reality) ? date('H:i', strtotime($item->ETD_time_reality)) : null;
                $item->ETA_time_reality = isset($item->ETA_time_reality) ? date('H:i', strtotime($item->ETA_time_reality)) : null;

                $results[] = $item;
                $max = max(count($item->listLocationDestinations), count($item->listLocationArrivals));
                if ($max > 1) {
                    for ($j = 1; $j < $max; $j++) {
                        $temp = new stdClass();
                        $temp->order_code = $item->order_code;
                        $temp->ETD_date = isset($item->listLocationDestinations[$j]) ? $item->listLocationDestinations[$j]->pivot->date : null;
                        $temp->ETA_date = isset($item->listLocationArrivals[$j]) ? $item->listLocationArrivals[$j]->pivot->date : null;
                        $temp->ETD_time = isset($item->listLocationDestinations[$j]) ? date('H:i', strtotime($item->listLocationDestinations[$j]->pivot->time)) : null;
                        $temp->ETA_time = isset($item->listLocationArrivals[$j]) ? date('H:i', strtotime($item->listLocationArrivals[$j]->pivot->time)) : null;
                        $temp->location_destination_id = isset($item->listLocationDestinations[$j]) ? $item->listLocationDestinations[$j]->id : null;
                        $temp->location_arrival_id = isset($item->listLocationArrivals[$j]) ? $item->listLocationArrivals[$j]->id : null;
                        $results[] = $temp;
                    }
                }
            }
        }
        $this->setViewData([
            'items' => $results
        ]);
        return $this->render();
    }

    public function columns()
    {
        $results = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'order');
        return json_encode($results);
    }

    public function vehicle()
    {
        $userID = Auth::User()->id;
        $results = $this->getVehicleRepository()->getItemsForSheet($userID);
        return json_encode($results);
    }

    public function customer()
    {
        $userID = Auth::User()->id;
        $results = $this->getCustomerRepository()->getItemsForSheet($userID);
        return json_encode($results);
    }

    public function customerDetail($id)
    {
        $result = $this->getCustomerRepository()->getItemById($id);
        return json_encode($result);
    }

    public function driver()
    {
        $userID = Auth::User()->id;
        $results = $this->getDriverRepository()->getItemsForSheet($userID);
        return json_encode($results);
    }

    public function location()
    {
        $userID = Auth::User()->id;
        $results = $this->getLocationRepository()->getItemsForSheet($userID);
        return json_encode($results);
    }

    public function user()
    {
        $userID = Auth::User()->id;
        $results = $this->getAdminUserRepository()->getItemsForSheet($userID);
        return json_encode($results);
    }

    public function import()
    {
        $validators = [];
        $excelColumnConfig = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'order');
        $excelColumnConfigMap = $excelColumnConfig->excelColumnMappingConfigs->pluck('original_field', 'field')->toArray();
        $update = request()->get('update') == 'true';
        $dataImport = app('App\Http\Controllers\Backend\OrderController')->handleDataImport(request(), $excelColumnConfig, $excelColumnConfigMap, $update, true);
        $dataList = $dataImport[0];

        $ignoreCount = 0;
        foreach ($dataList as $row) {
            if (!empty($row['error'])) {
                $validators[$row['row'] - $excelColumnConfig->header_index] = $row['error'];
                $ignoreCount++;
            }
        }
        if ($ignoreCount > 0) {
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => "",
                'data' => $validators
            ]);
        } else {
            $ignoreCount = app('App\Http\Controllers\Backend\OrderController')->handleFileImport($dataList, $update, true);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => "",
                'data' => ['total' => count($dataList), '$ignoreCount' => $ignoreCount]
            ]);
        }
    }
}
