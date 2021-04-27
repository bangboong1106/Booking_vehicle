<?php

namespace App\Http\Controllers\Backend;

use App\Common\HttpCode;
use App\Exports\OrdersExport;
use App\Exports\OrderConverter;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\TemplateExcelConverterRepository;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DriverRepository;
use App\Repositories\GoodsTypeRepository;
use App\Repositories\GoodsUnitRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReceiptPaymentRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\ExcelColumnConfigRepository;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ExcelConverterController extends BackendController
{
    protected $_templateExcelConverterRepository;
    protected $_orderRepository;
    protected $_customerRepository;
    protected $_locationRepository;
    protected $_goodsTypeRepository;
    protected $_vehicleRepository;
    protected $_driverRepository;
    protected $_goodsUnitRepository;
    protected $_routesRepository;
    protected $_quotaRepository;
    protected $_adminUserRepository;
    protected $_receiptPaymentRepository;
    protected $_orderCustomerRepository;
    protected $_excelColumnConfigRepository;

    public function getTemplateExcelConverterRepository()
    {
        return $this->_templateExcelConverterRepository;
    }

    public function setTemplateExcelConverterRepository($templateExcelConverterRepository): void
    {
        $this->_templateExcelConverterRepository = $templateExcelConverterRepository;
    }

    public function getOrderRepository()
    {
        return $this->_orderRepository;
    }

    public function setOrderRepository($orderRepository): void
    {
        $this->_orderRepository = $orderRepository;
    }

    public function getRoutesRepository()
    {
        return $this->_routesRepository;
    }

    public function setRoutesRepository($routesRepository): void
    {
        $this->_routesRepository = $routesRepository;
    }

    public function getCustomerRepository()
    {
        return $this->_customerRepository;
    }

    public function setCustomerRepository($customerRepository): void
    {
        $this->_customerRepository = $customerRepository;
    }

    public function getGoodsTypeRepository()
    {
        return $this->_goodsTypeRepository;
    }

    public function setGoodsTypeRepository($goodsTypeRepository): void
    {
        $this->_goodsTypeRepository = $goodsTypeRepository;
    }

    public function getVehicleRepository()
    {
        return $this->_vehicleRepository;
    }

    public function setVehicleRepository($vehicleRepository): void
    {
        $this->_vehicleRepository = $vehicleRepository;
    }

    public function getGoodsUnitRepository()
    {
        return $this->_goodsUnitRepository;
    }

    public function setGoodsUnitRepository($goodsUnitRepository): void
    {
        $this->_goodsUnitRepository = $goodsUnitRepository;
    }

    public function getLocationRepository()
    {
        return $this->_locationRepository;
    }

    public function setLocationRepository($locationRepository): void
    {
        $this->_locationRepository = $locationRepository;
    }

    public function getReceiptPaymentRepository()
    {
        return $this->_receiptPaymentRepository;
    }

    public function setReceiptPaymentRepository($receiptPaymentRepository)
    {
        $this->_receiptPaymentRepository = $receiptPaymentRepository;
    }

    public function setTemplateRepository($templateRepository)
    {
        $this->_templateRepository = $templateRepository;
    }

    public function getExcelColumnConfigRepository()
    {
        return $this->_excelColumnConfigRepository;
    }

    public function setExcelColumnConfigRepository($excelColumnConfigRepository): void
    {
        $this->_excelColumnConfigRepository = $excelColumnConfigRepository;
    }

    public function __construct(
        TemplateExcelConverterRepository $templateExcelConverterRepository,
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        LocationRepository $locationRepository,
        GoodsTypeRepository $GoodsTypeRepository,
        VehicleRepository $vehicleRepository,
        DriverRepository $driverRepository,
        GoodsUnitRepository $GoodsUnitRepository,
        RoutesRepository $routesRepository,
        AdminUserInfoRepository $adminUserInfoRepository,
        ReceiptPaymentRepository $receiptPaymentRepository,
        OrderCustomerRepository $orderCustomerRepository,
        ExcelColumnConfigRepository $excelColumnConfigRepository
    ) {
        parent::__construct();
        $this->setTemplateExcelConverterRepository($templateExcelConverterRepository);
        $this->setOrderRepository($orderRepository);
        $this->setCustomerRepository($customerRepository);
        $this->setLocationRepository($locationRepository);
        $this->setGoodsTypeRepository($GoodsTypeRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setDriverRepository($driverRepository);
        $this->setGoodsUnitRepository($GoodsUnitRepository);
        $this->setRoutesRepository($routesRepository);
        $this->setAdminUserRepository($adminUserInfoRepository);
        $this->setReceiptPaymentRepository($receiptPaymentRepository);
        $this->setOrderCustomerRepository($orderCustomerRepository);
        $this->setExcelColumnConfigRepository($excelColumnConfigRepository);
        $this->setMenu('order');
    }

    public function getOrderCustomerRepository()
    {
        return $this->_orderCustomerRepository;
    }

    public function setOrderCustomerRepository($orderCustomerRepository)
    {
        $this->_orderCustomerRepository = $orderCustomerRepository;
    }

    public function getAdminUserRepository()
    {
        return $this->_adminUserRepository;
    }

    public function setAdminUserRepository($adminUserRepository)
    {
        $this->_adminUserRepository = $adminUserRepository;
    }

    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }

    public function index()
    {
        $this->setTitle(trans('Chuyển đổi dữ liệu Excel'));
        $templates = $this->getTemplateExcelConverterRepository()->pluck('title as name', 'id');
        $this->setViewData([
            'templates' => $templates
        ]);
        return $this->render();
    }

    public function convert(Request $request)
    {
        try {
            ini_set('max_execution_time', 8000000);

            $templatePath = Request::get('path');
            $templateId = Request::get('template_id');

            $template = $this->getTemplateExcelConverterRepository()->getItemById($templateId);

            $currentUser = $this->getCurrentUser();
            if (null == $currentUser || !$currentUser) {
                // Lay account admin ra de lam du lieu default
                $currentUser = $this->getAdminUserRepository()->getAdminUserByUserName('admin');
            }

            $orderExport = new OrdersExport(
                $this->getOrderRepository(),
                $this->getCustomerRepository(),
                $this->getGoodsTypeRepository(),
                $this->getDriverRepository(),
                $this->getVehicleRepository(),
                $this->getGoodsUnitRepository(),
                $this->getLocationRepository(),
                $this->getReceiptPaymentRepository(),
                $this->getOrderCustomerRepository(),
                $this->getAdminUserRepository(),
                $this->_getDataIndex()
            );

            $orderExport->is_update = false;
            $orderExport->excelColumnConfig = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'order');
            $orderPath = $orderExport->generateExcelFile($currentUser->id, true);


            $orderConverter = new OrderConverter(
                $this->getOrderRepository(),
                $this->getCustomerRepository(),
                $this->getGoodsTypeRepository(),
                $this->getDriverRepository(),
                $this->getVehicleRepository(),
                $this->getGoodsUnitRepository(),
                $this->getLocationRepository(),
                $this->getReceiptPaymentRepository(),
                $this->getOrderCustomerRepository(),
                $this->getAdminUserRepository(),
                $this->_getDataIndex()
            );

            $filePath = $orderConverter->convert($template, $templatePath, $orderPath, $orderExport->excelColumnConfig);
            if (empty($filePath)) {
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => "Có lỗi khi download"
                ]);
            }
            $nameFile = 'DanhSachDonHangChuyenDoi_' . Carbon::now()->format('d_m_Y') . '.xlsx';

            return Response::download($filePath, $nameFile, []);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage()
            ]);
        }
    }
}
