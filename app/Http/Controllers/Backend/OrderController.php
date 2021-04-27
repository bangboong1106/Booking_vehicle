<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Common\GoogleConstant;
use App\Common\HttpCode;
use App\Events\OrderExcelEvent;
use App\Exports\OrdersExport;
use App\Exports\TemplateExport;
use App\Helpers\Facades\BatchFacade as Batch;
use App\Http\Controllers\Base\BackendController;
use App\Imports\OrderImport;
use App\Model\Entities\Order;
use App\Model\Entities\OrderGood;
use App\Model\Entities\OrderLocation;
use App\Model\Entities\OrderPayment;
use App\Model\Entities\Routes;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\ContactRepository;
use App\Repositories\CurrencyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\GoodsTypeRepository;
use App\Repositories\GoodsGroupRepository;
use App\Repositories\GoodsUnitRepository;
use App\Repositories\LocationRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderCustomerReviewRepository;
use App\Repositories\OrderFileRepository;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\QuotaRepository;
use App\Repositories\ReceiptPaymentRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\TPActionSyncRepository;
use App\Repositories\TPApiConfigRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\WardRepository;
use App\Repositories\ExcelColumnConfigRepository;
use App\Services\NotificationService;
use App\Services\OrderCustomerService;
use App\Services\OrderService;
use App\Services\RouteService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use QrCode;
use Validator;

class OrderController extends BackendController
{
    protected $_customerRepository;
    protected $_provinceRepository;
    protected $_locationRepository;
    protected $_goodsTypeRepository;
    protected $_goodsGroupRepository;
    protected $_orderHistoryRepository;
    protected $_vehicleRepository;
    protected $_fileRepository;
    protected $_orderFileRepository;
    protected $_driverRepository;
    protected $_goodsUnitRepository;
    protected $_currencyRepository;
    protected $_contactRepository;
    protected $_columnConfigRepository;
    protected $_routesRepository;
    protected $_quotaRepository;
    protected $_districtRepository;
    protected $_wardRepository;
    protected $_adminUserRepository;
    protected $_receiptPaymentRepository;
    protected $_templateRepository;
    protected $orderEntityOld = null;
    protected $_orderCustomerRepository;
    protected $_tpActionSyncRepository;
    protected $tpApiRepos;
    protected $orderCustomerReviewRepos;
    protected $_orderPaymentRepos;
    protected $_excelColumnConfigRepository;
    protected $_partnerRepository;
    protected $_orderCustomerService;
    protected $_routeService;
    protected $_orderService;
    protected $_notificationService;

    /*
     * Thông tin liên hệ
     */
    public function setContactRepository($contactRepository): void
    {
        $this->_contactRepository = $contactRepository;
    }

    /**
     * @return ContactRepository
     */
    public function getContactRepository()
    {
        return $this->_contactRepository;
    }

    /**
     * @param $currencyRepository
     */
    public function setCurrencyRepository($currencyRepository): void
    {
        $this->_currencyRepository = $currencyRepository;
    }

    /**
     * @return CurrencyRepository
     */
    public function getCurrencyRepository()
    {
        return $this->_currencyRepository;
    }

    /**
     * @return GoodsTypeRepository
     */
    public function getGoodsTypeRepository()
    {
        return $this->_goodsTypeRepository;
    }

    /**
     * @param $goodsTypeRepository
     */
    public function setGoodsTypeRepository($goodsTypeRepository): void
    {
        $this->_goodsTypeRepository = $goodsTypeRepository;
    }


    public function getGoodsGroupRepository()
    {
        return $this->_goodsGroupRepository;
    }


    public function setGoodsGroupRepository($goodsGroupRepository): void
    {
        $this->_goodsGroupRepository = $goodsGroupRepository;
    }

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
     * @return GoodsUnitRepository
     */
    public function getGoodsUnitRepository()
    {
        return $this->_goodsUnitRepository;
    }

    /**
     * @param mixed $goodsUnitRepository
     */
    public function setGoodsUnitRepository($goodsUnitRepository): void
    {
        $this->_goodsUnitRepository = $goodsUnitRepository;
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
     * @return DistrictRepository
     */
    public function getDistrictRepository()
    {
        return $this->_districtRepository;
    }

    /**
     * @param $districtRepository
     */
    public function setDistrictRepository($districtRepository): void
    {
        $this->_districtRepository = $districtRepository;
    }

    /**
     * @return WardRepository
     */
    public function getWardRepository()
    {
        return $this->_wardRepository;
    }

    /**
     * @param $wardRepository
     */
    public function setWardRepository($wardRepository): void
    {
        $this->_wardRepository = $wardRepository;
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
     * @param $templateRepository
     */
    public function setTemplateRepository($templateRepository)
    {
        $this->_templateRepository = $templateRepository;
    }

    /**
     * @return TemplateRepository
     */
    public function getTemplateRepository()
    {
        return $this->_templateRepository;
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
     * @return TPActionSyncRepository
     */
    public function getTPActionSyncRepository()
    {
        return $this->_tpActionSyncRepository;
    }

    /**
     * @param mixed $tpActionSyncRepository
     */
    public function setTPActionSyncRepository($tpActionSyncRepository)
    {
        $this->_tpActionSyncRepository = $tpActionSyncRepository;
    }

    public function getTpApiRepos()
    {
        return $this->tpApiRepos;
    }

    public function setTpApiRepos($tpApiRepos)
    {
        $this->tpApiRepos = $tpApiRepos;
    }

    public function getOrderCustomerReviewRepos()
    {
        return $this->orderCustomerReviewRepos;
    }

    public function setOrderCustomerReviewRepos($orderCustomerReviewRepos)
    {
        $this->orderCustomerReviewRepos = $orderCustomerReviewRepos;
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

    /**
     * @return mixed
     */
    public function getPartnerRepository()
    {
        return $this->_partnerRepository;
    }

    /**
     * @param mixed $partnerRepository
     */
    public function setPartnerRepository($partnerRepository): void
    {
        $this->_partnerRepository = $partnerRepository;
    }

    /**
     * @return mixed
     */
    public function getOrderCustomerService()
    {
        return $this->_orderCustomerService;
    }

    /**
     * @param mixed $orderCustomerService
     */
    public function setOrderCustomerService($orderCustomerService): void
    {
        $this->_orderCustomerService = $orderCustomerService;
    }

    /**
     * @return mixed
     */
    public function getRouteService()
    {
        return $this->_routeService;
    }

    /**
     * @param mixed $routeService
     */
    public function setRouteService($routeService): void
    {
        $this->_routeService = $routeService;
    }

    /**
     * @return mixed
     */
    public function getOrderService()
    {
        return $this->_orderService;
    }

    /**
     * @param mixed $orderService
     */
    public function setOrderService($orderService): void
    {
        $this->_orderService = $orderService;
    }

    /**
     * @return mixed
     */
    public function getNotificationService()
    {
        return $this->_notificationService;
    }

    /**
     * @param mixed $notificationService
     */
    public function setNotificationService($notificationService): void
    {
        $this->_notificationService = $notificationService;
    }

    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        ProvinceRepository $provinceRepository,
        LocationRepository $locationRepository,
        GoodsTypeRepository $GoodsTypeRepository,
        GoodsGroupRepository $goodsGroupRepository,
        OrderHistoryRepository $orderHistoryRepository,
        VehicleRepository $vehicleRepository,
        FileRepository $fileRepository,
        OrderFileRepository $orderFileRepository,
        DriverRepository $driverRepository,
        GoodsUnitRepository $GoodsUnitRepository,
        CurrencyRepository $currencyRepository,
        ContactRepository $contactRepository,
        ColumnConfigRepository $columnConfigRepository,
        RoutesRepository $routesRepository,
        QuotaRepository $quotaRepository,
        DistrictRepository $districtRepository,
        WardRepository $wardRepository,
        AdminUserInfoRepository $adminUserInfoRepository,
        ReceiptPaymentRepository $receiptPaymentRepository,
        TemplateRepository $templateRepository,
        OrderCustomerRepository $orderCustomerRepository,
        TPActionSyncRepository $tpActionSyncRepository,
        TPApiConfigRepository $tpApiConfigRepository,
        OrderCustomerReviewRepository $customerReviewRepository,
        OrderPaymentRepository $orderPaymentRepository,
        ExcelColumnConfigRepository $excelColumnConfigRepository,
        PartnerRepository $partnerRepository,
        OrderCustomerService $orderCustomerService,
        RouteService $routeService,
        OrderService $orderService,
        NotificationService $notificationService
    )
    {
        parent::__construct();
        set_time_limit(0);
        $this->setRepository($orderRepository);
        $this->setBackUrlDefault('order.index');
        $this->setConfirmRoute('order.confirm');
        $this->setMenu('order');
        $this->setTitle(trans('models.order.name'));

        $this->setCustomerRepository($customerRepository);
        $this->setProvinceRepository($provinceRepository);
        $this->setLocationRepository($locationRepository);
        $this->setGoodsTypeRepository($GoodsTypeRepository);
        $this->setGoodsGroupRepository($goodsGroupRepository);
        $this->setOrderHistoryRepository($orderHistoryRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setFileRepository($fileRepository);
        $this->setOrderFileRepository($orderFileRepository);
        $this->setDriverRepository($driverRepository);
        $this->setGoodsUnitRepository($GoodsUnitRepository);
        $this->setCurrencyRepository($currencyRepository);
        $this->setContactRepository($contactRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setRoutesRepository($routesRepository);
        $this->setQuotaRepository($quotaRepository);
        $this->setDistrictRepository($districtRepository);
        $this->setWardRepository($wardRepository);
        $this->setAdminUserRepository($adminUserInfoRepository);
        $this->setReceiptPaymentRepository($receiptPaymentRepository);
        $this->setTemplateRepository($templateRepository);
        $this->setOrderCustomerRepository($orderCustomerRepository);
        $this->setTPActionSyncRepository($tpActionSyncRepository);
        $this->setTpApiRepos($tpApiConfigRepository);
        $this->setOrderCustomerReviewRepos($customerReviewRepository);
        $this->setOrderPaymentRepos($orderPaymentRepository);
        $this->setExcelColumnConfigRepository($excelColumnConfigRepository);
        $this->setPartnerRepository($partnerRepository);
        $this->setOrderCustomerService($orderCustomerService);
        $this->setRouteService($routeService);
        $this->setOrderService($orderService);
        $this->setNotificationService($notificationService);

        $this->setMap(true);
        $this->setExcel(false);
        $this->setAuditing(true);
        $this->setDeleted(true);
        $this->setViewData([
            'exampleName' => 'Danh_sach_don_hang.xlsx',
            'urlTemplate' => route('order.exportTemplate'),
            'googleSheetUrl' => env('FILE_GD_URL'),
            'editGoogleSheetUrl' => env('FILE_GD_EDIT_URL'),

        ]);
    }

    public function _prepareForm()
    {
        $goodsUnit = $this->getGoodsUnitRepository()->getListForSelect()->toArray();
        $goodsUnits = Arr::prepend($goodsUnit, 'Đơn vị', '0');

        $goodsGroup = $this->getGoodsGroupRepository()->getScopedNestedList('name', 'id', '-', false);
        $goodsGroup = Arr::prepend($goodsGroup, '', null);


        $this->setViewData([
            'customers' => $this->getCustomerRepository()->search()->get(),
            'provinceList' => $this->getProvinceRepository()->getListForSelect(),
            'goodsTypes' => $this->getGoodsTypeRepository()->getListForSelect(),
            'goodsUnits' => $goodsUnits,
            'districtList' => [],
            'wardList' => [],
            'currencyList' => $this->getCurrencyRepository()->getListForSelect(),
            'commissionType' => config('system.order_commission_type'),
            'goodsGroups' => $goodsGroup,
            'partnerList' => $this->getPartnerRepository()->getListForSelect()
        ]);
    }

    public function store()
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.create_failed'));
        }
        try {
            DB::beginTransaction();
            $entity = $this->_findEntityForStore();
            if ($this->getRepository()->existSystemCode('orders', 'order_code', $entity->order_code)) {
                return $this->_backToStart()->withErrors(trans('messages.create_failed'));
            }
            $this->fireEvent('before_store', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $entity = $this->_processOrderBusiness($entity);
            $entity->source_create = config("constant.SOURCE_CREATE_ORDER_FORM");
            $entity->save();

            //Trigger tạo bản ghi đồng bộ đối tác
            $this->getTPActionSyncRepository()->triggerActionSync(null, $entity);
            $this->_saveRelations($entity);

            $data = $this->_getFormData(false, true);
            $this->_processCreateRelation($entity, $data);

            // add new
            $this->fireEvent('after_store', $entity);
            DB::commit();

            $renew = Request::get('renew');
            if (isset($renew)) {
                return $this->_to('order.create')->with('success', trans('messages.create_success'));
            }
            return $this->_backToStart()->with('success', trans('messages.create_success'));
        } catch (Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.create_failed'));
    }

    public function update($id)
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.update_failed'));
        }
        $isValid = $this->getRepository()->getValidator()->validateShow($id);
        if (!$isValid) {
            return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
        }
        try {
            DB::beginTransaction();
            $entity = $this->_findEntityForUpdate($id);
            $this->fireEvent('before_update', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $this->orderEntityOld = $this->getRepository()->getItemById($entity->id);
            $entity = $this->_processOrderBusiness($entity);
            $entity->save();

            //Trigger tạo bản ghi đồng bộ đối tác
            $this->getTPActionSyncRepository()->triggerActionSync($this->orderEntityOld, $entity);
            // fire after save
            // fire before save relation
            $this->_saveRelations($entity, 'save');

            $data = $this->_getFormData(false, true);
            $this->_processCreateRelation($entity, $data, true, $this->orderEntityOld);
            // fire after save relation
            // add new
            DB::commit();
            $this->fireEvent('after_update', $entity);

            $renew = Request::get('renew');
            if (isset($renew)) {
                return $this->_to('order.create')->with('success', trans('messages.update_success'));
            }

            return $this->_backToStart()->with('success', trans('messages.update_success'));
        } catch (Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.update_failed'));
    }

    public function valid()
    {
        $params = $this->_getParams();
        if (isset($params['locationDestinations']) && count($params['locationDestinations']) > 0) {
            if ($params['status'] != config('constant.HOAN_THANH') && $params['status'] != config('constant.DANG_VAN_CHUYEN')) {
                $params['locationDestinations'][0]['date_reality'] = null;
                $params['locationDestinations'][0]['time_reality'] = null;
                $params['ETD_date_reality'] = null;
                $params['ETD_time_reality'] = null;
            }
        }
        if (isset($params['locationArrivals']) && count($params['locationArrivals']) > 0) {
            if ($params['status'] != config('constant.HOAN_THANH')) {
                $params['locationArrivals'][0]['date_reality'] = null;
                $params['locationArrivals'][0]['time_reality'] = null;
                $params['ETA_date_reality'] = null;
                $params['ETA_time_reality'] = null;
            }
        }

        $params['insured_goods'] = empty($params['insured_goods']) ? 0 : $params['insured_goods'];
        $this->fireEvent('before_valid', $params);
        $this->_setFormData($params);
        $this->_processFile();

        if (Request::has('id') && Request::get('id')) {
            if (!$this->getRepository()->getValidator()->validateUpdate($params)) {
                return $this->_inValid();
            }
        } else {
            // case create
            if (!$this->getRepository()->getValidator()->validateCreate($params)) {
                return $this->_inValid();
            }
        }
        $this->_setFormData($params);

        $error = $this->_validStatusFollowField($params);
        if (!empty($error)) {
            return $this->_back()->withInput()->with(['status' => $error]);
        }

        // TODO: áp dụng validate thông tin chứng từ
        //    $error = $this->_validStatusDocumentField($params);
        //    if (!empty($error)) {
        //        return $this->_back()->withInput()->with(['status_collected_documents' => $error]);
        //    }

        $errorRoute = $this->_validRoute($params);
        if (!empty($errorRoute)) {
            return $this->_back()->withInput()->with(['route_error' => $errorRoute]);
        }

        $result = $this->getConfirmRoute() ? $this->_toConfirm() : $this->forward($this->getCurrentController(), 'store');
        $this->fireEvent('after_valid', $result);
        return $result;
    }

    public function _saveOrderFile($data, $order_id)
    {
        $order_status_file_list = $order_status_file_list = config("system.order_status_file");;

        $this->getOrderFileRepository()->deleteWhere([
            'order_id' => $order_id
        ]);

        foreach ($order_status_file_list as $order_status) {

            if (!isset($data['order_file']))
                continue;

            $order_file = $data['order_file'][$order_status['id']];

            if (isset($order_file['file_id'])) {
                $file_id_list = explode(';', $order_file['file_id']);

                foreach ($file_id_list as $file_id) {

                    $orderFileEntity = $this->getOrderFileRepository()->findFirstOrNew([]);

                    $orderFileEntity->order_id = $order_id;
                    $orderFileEntity->order_status = $order_status['id'];
                    $orderFileEntity->file_id = $file_id;
                    if (isset($order_file['reason'])) {
                        $orderFileEntity->reason = $order_file['reason'];
                    }

                    $orderFileEntity->save();
                    app('App\Http\Controllers\Backend\FileController')->moveFileFromTmpToMedia($orderFileEntity->file_id, 'orders');
                }
            } else {
                if ($order_status['id'] != config("constant.KHOI_TAO")) {
                    $orderFileEntity = $this->getOrderFileRepository()->findFirstOrNew([]);
                    $orderFileEntity->order_id = $order_id;
                    $orderFileEntity->order_status = $order_status['id'];
                    $orderFileEntity->reason = $order_file['reason'];

                    $orderFileEntity->save();
                }
            }
        }
    }

    protected function _prepareAfterSetEntity($prepare)
    {
        if ($prepare instanceof RedirectResponse) {
            return;
        }

        /** @var Order $entity */
        $entity = $this->getEntity();
        $this->getSelected($entity);
    }

    protected function _prepareFormWithID($id)
    {
        // Get driver config
        $attributes = $this->_getFormData(false);

        $order_status_file_list = config("system.order_status_file");;
        $order_file_list = [];

        $isDuplicate = false;
        if (strpos($this->getCurrentRouteName(), 'duplicate')) {
            $isDuplicate = true;
        }

        if (array_key_exists('order_file', $attributes)) {
            // Put lại data file cho form khi nhấn back
            foreach ($order_status_file_list as $order_status_file) {

                $order_file = $attributes['order_file'][$order_status_file['id']];
                $order_files = null;
                if (!empty($order_file['file_id'])) {
                    $file_id_list = explode(';', $order_file['file_id']);
                    if (!empty($file_id_list)) {
                        foreach ($file_id_list as $file_id) {
                            $file = $this->getFileRepository()->getFileWithID($file_id);
                            if (!is_null($file)) {
                                $reason = "";
                                if (isset($order_file['reason'])) {
                                    $reason = $order_file['reason'];
                                }
                                $orderFileEntity = [
                                    'file_name' => $file->file_name,
                                    'size' => $file->size,
                                    'file_id' => $file_id,
                                    'reason' => $reason,
                                ];
                                $order_files[] = $orderFileEntity;
                            } else {
                                unset($order_files[$file_id]);
                            }
                        }
                    }
                } else {
                    if ($order_status_file['id'] != config("constant.KHOI_TAO")) {
                        $reason = "";
                        if (isset($order_file['reason']))
                            $reason = $order_file['reason'];
                        $orderFileEntity = [
                            'reason' => $reason,
                        ];
                        $order_files[] = $orderFileEntity;
                    }
                }
                $order_file_list[$order_status_file['id']] = collect($order_files);
            }
        } else {
            foreach ($order_status_file_list as $order_status_file) {
                if (!$isDuplicate) {
                    if ($id != -1) {
                        $order_files = $this->getOrderFileRepository()->getOrderFile($id, $order_status_file['id']);
                        if ($order_files) {
                            foreach ($order_files as $key => $order_file) {
                                $file = $this->getFileRepository()->getFileWithID($order_file->file_id);
                                if (!is_null($file)) {
                                    $order_file['file_name'] = $file->file_name;
                                    $order_file['size'] = $file->size;
                                } else {
                                    unset($order_files[$key]);
                                }
                            }
                        }
                        $order_file_list[$order_status_file['id']] = $order_files;
                    } else {
                        $order_file_list[$order_status_file['id']] = null;
                    }
                } else {
                    $order_file_list[$order_status_file['id']] = null;
                }
            }
        }

        $code = null;
        if (array_key_exists('order_code', $attributes)) {
            $code = $attributes['order_code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order'));
            }
        }

        $this->setViewData([
            'order_status_file_list' => $order_status_file_list,
            'order_file_list' => $order_file_list,
            'order_code' => $code,
        ]);
    }

    public function advance(Request $request)
    {
        $autoload = '';
        $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order'));

        if ($request->isMethod('get')) {
            $this->_getFormData(false, true);
            $this->_prepareCreate();
            $model = Request::get('model', '');

            $this->setViewData([
                'formAdvance' => true,
                'fastOrder' => $this->getRepository()->findFirstOrNew([], false),
                'customers' => $this->getCustomerRepository()->search()->get(),
                'order_code' => $code
            ]);

            $content_view = $this->render('backend.' . $this->getCurrentControllerName() . '._fast_order')->render();

            $html = [
                'content' => $content_view . $autoload,
                'title' => empty($model) ? '' : transb($model . '.create'),
            ];

            $this->setData($html);
        }

        if ($request->isMethod('post')) {
            try {
                DB::beginTransaction();

                $params = $this->_getParams();
                $error = $this->_validStatusFollowField($params);
                $model = Request::get('model', '');
                $this->_setFormData($params);

                $this->setViewData([
                    'formAdvance' => true,
                    'fastOrder' => $this->getRepository()->findFirstOrNew($params, false),
                    'customers' => $this->getCustomerRepository()->search()->get(),
                    'order_code' => $code
                ]);

                if (!$this->getRepository()->getValidator()->validateCreate($params)) {
                    $this->_prepareCreate();
                    $listErrorMessage = $this->_getListErrorMessage();
                    $this->setViewData([
                        'validation' => ['inValid' => $listErrorMessage]
                    ]);

                    $content_view = $this->render('backend.' . $this->getCurrentControllerName() . '._fast_order')->render();

                    $html = [
                        'content' => $content_view . $autoload,
                        'title' => empty($model) ? '' : transb($model . '.create'),
                        'validation' => true
                    ];
                } elseif (!empty($error)) {
                    $this->_prepareCreate();
                    $listErrorMessage['status'] = $error;
                    $this->setViewData([
                        'validation' => ['inValid' => $listErrorMessage]
                    ]);

                    $content_view = $this->render('backend.' . $this->getCurrentControllerName() . '._fast_order')->render();

                    $html = [
                        'content' => $content_view . $autoload,
                        'title' => empty($model) ? '' : transb($model . '.create'),
                        'validation' => true
                    ];
                } else {
                    $entity = $this->_findEntityForStore();
                    $entity->order_no = $entity->code;
                    $entity->is_collected_documents = config("constant.no");
                    $entity->is_insured_goods = config("constant.no");
                    $entity->status_collected_documents = config("constant.CHUA_THU_DU");
                    $entity->commission_type = config("constant.TONG_TIEN_HOA_HONG");
                    $entity->commission_value = 0;
                    $entity->source_create = config("constant.SOURCE_CREATE_ORDER_ADVANCE");
                    try {
                        $entity->save();
                        $this->_saveRelations($entity);
                        $data = [
                            'route_create' => 1,
                            'vehicle_id' => isset($params['vehicle_id']) ? $params['vehicle_id'] : null,
                            'primary_driver_id' => isset($params['primary_driver_id']) ? $params['primary_driver_id'] : null,
                            'secondary_driver_id' => isset($params['secondary_driver_id']) ? $params['secondary_driver_id'] : null
                        ];
                        $this->_processCreateRelation($entity, $data);
                    } catch (Exception $e) {
                        logError($e);
                    }

                    $html = [
                        'title' => empty($model) ? '' : transb($model . '.create'),
                        'entity' => $entity->toArray(),
                        'model' => $model
                    ];
                }

                $this->setData($html);

                DB::commit();
            } catch (Exception $exception) {
                DB::rollBack();
                logError($exception);
            }
        }

        return $this->renderJson();
    }

    protected function _findEntityForUpdate($id)
    {
        $entity = $this->_findOrNewEntity($id, false, true);
        return $this->_processInputData($entity);
    }

    protected function _findEntityForStore()
    {
        $entity = $this->_findOrNewEntity(null, false);
        return $this->_processInputData($entity);
    }

    protected function _prepareCreate()
    {
        $parent = parent::_prepareCreate();
        $entity = $this->getEntity();
        /** @var Order $entity */
        $this->getSelected($entity);
        return $parent;
    }

    protected function _prepareConfirm()
    {
        $entity = $this->_getFormData();
        if (isset($entity->ETD_date_reality)) {
            if ($entity->status != config('constant.HOAN_THANH') && $entity->status != config('constant.DANG_VAN_CHUYEN')) {
                $entity->ETD_date_reality = null;
                $entity->ETD_time_reality = null;
            }
        }
        if (isset($entity->ETA_date_reality)) {
            if ($entity->status != config('constant.HOAN_THANH')) {
                $entity->ETA_date_reality = null;
                $entity->ETA_time_reality = null;
            }
        }
        $entity = $this->_processOrderBusiness($entity);
        $this->setEntity($entity);

        //Xử lý khi nhập freetext địa điểm.
        if (isset($entity->locationDestinations)) {
            foreach ($entity->locationDestinations as $index => &$location) {
                if (!empty($location['location_id']) && is_string($location['location_id']) && strlen($location['location_id']) > 2 && strpos($location['location_id'], 'id') === 0) {
                    $location['location_id'] = substr_replace($location['location_id'], '', 0, strlen('id'));
                }
                if ($index == 0) {
                    if ($entity->status == config('constant.HOAN_THANH')) {
                        $entity->ETD_date_reality = $location['date_reality'];
                        $entity->ETD_time_reality = $location['time_reality'];
                    }
                }
            }
        }
        if (isset($entity->locationArrivals)) {
            foreach ($entity->locationArrivals as $index => &$location) {
                if (!empty($location['location_id']) && is_string($location['location_id']) && strlen($location['location_id']) > 2 && strpos($location['location_id'], 'id') === 0) {
                    $location['location_id'] = substr_replace($location['location_id'], '', 0, strlen('id'));
                }
                if ($index == 0) {
                    if ($entity->status == config('constant.HOAN_THANH')) {
                        $entity->ETA_date_reality = $location['date_reality'];
                        $entity->ETA_time_reality = $location['time_reality'];
                    }
                }
            }
        }

        $this->_prepareDisplay($entity);

        $data = $this->_getFormData()->getAttributes();
        $order_status_file_list = config("system.order_status_file");
        $order_file_list = [];
        if (array_key_exists('order_file', $data))
            $order_file_list = $data['order_file'];
        $vehicle = null;
        if (isset($data['vehicle_id'])) {
            $vehicle = $this->getVehicleRepository()->getItemById($data['vehicle_id']);
        }
        $primary_driver = null;
        if (isset($data['primary_driver_id'])) {
            $primary_driver = $this->getDriverRepository()->getItemById($data['primary_driver_id']);
        }
        $secondary_driver = null;
        if (isset($data['secondary_driver_id'])) {
            $secondary_driver = $this->getDriverRepository()->getItemById($data['secondary_driver_id']);
        }

        $route = null;
        $message = [];
        if (array_key_exists('route_id', $data) && !empty($data['route_id']) && isset($vehicle)) {
            $route = $this->getRoutesRepository()->getItemById($data['route_id']);
            $message = $this->_calcCapacity($route, $entity, $vehicle);
        }

        $this->setViewData([
            'order_status_file_list' => $order_status_file_list,
            'order_file_list' => $order_file_list,
            'show_history' => false,
            'vehicle' => $vehicle,
            'primary_driver' => $primary_driver,
            'secondary_driver' => $secondary_driver,
            'route' => $route,
            'messages' => $message
        ]);

        return $entity;
    }

    /**
     * @param $entity
     * @return Order
     */
    public function _processInputData($entity)
    {
        if ($entity->status == config("constant.DANG_VAN_CHUYEN") || $entity->status == config("constant.HOAN_THANH")) {
            if (isset($entity->ETD_date_reality))
                $entity->ETD_date_reality = empty($entity->ETD_date_reality) ? null : AppConstant::convertDate($entity->ETD_date_reality, 'Y-m-d');
        } else {
            $entity->ETD_date_reality = null;
            $entity->ETD_time_reality = null;
        }

        if ($entity->status == config("constant.HOAN_THANH")) {
            if (isset($entity->ETA_date_reality))
                $entity->ETA_date_reality = empty($entity->ETA_date_reality) ? null : AppConstant::convertDate($entity->ETA_date_reality, 'Y-m-d');
        } else {
            $entity->ETA_date_reality = null;
            $entity->ETA_time_reality = null;
        }

        if (isset($entity->ETD_date))
            $entity->ETD_date = empty($entity->ETD_date) ? null : AppConstant::convertDate($entity->ETD_date, 'Y-m-d');
        if (isset($entity->ETA_date))
            $entity->ETA_date = empty($entity->ETA_date) ? null : AppConstant::convertDate($entity->ETA_date, 'Y-m-d');
        if (isset($entity->order_date))
            $entity->order_date = empty($entity->order_date) ? null : AppConstant::convertDate($entity->order_date, 'Y-m-d');
        if (isset($entity->date_collected_documents))
            $entity->date_collected_documents = empty($entity->date_collected_documents) ? null : AppConstant::convertDate($entity->date_collected_documents, 'Y-m-d');
        if (isset($entity->date_collected_documents_reality))
            $entity->date_collected_documents_reality = empty($entity->date_collected_documents_reality) ? null : AppConstant::convertDate($entity->date_collected_documents_reality, 'Y-m-d');

        return $entity;
    }

    protected function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_order'));
        $dayCondition = env('DAY_CONDITION_DEFAULT', 4);
        $this->setViewData([
            'statuses' => config('system.order_status'),
            'dayCondition' => $dayCondition,
            'precedences' => config('system.order_precedences'),
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"],
            'partnerList' => $this->getPartnerRepository()->getListForSelect()
        ]);
    }

    protected function _processOrderBusiness($entity)
    {
        //Tính phí hoa hồng
        $commission_amount = 0;
        if (isset($entity->commission_value) && is_numeric($entity->commission_value) && isset($entity->amount) && is_numeric($entity->amount)) {
            if ($entity->commission_type == config('constant.PHAN_TRAM_HOA_HONG')) {
                $commission_amount = $entity->amount * ($entity->commission_value / 100);
            } else if ($entity->commission_type == config('constant.TONG_TIEN_HOA_HONG')) {
                $commission_amount = $entity->commission_value;
            }
            $entity->commission_amount = $commission_amount;
        }

        $entity = app('App\Http\Controllers\Backend\DocumentController')->calcStatusDocument($entity);

        //Xử lý trạng thái đối tác vận tải
        if (in_array($entity->status, [config('constant.SAN_SANG'), config('constant.TAI_XE_XAC_NHAN'), config('constant.CHO_NHAN_HANG')
            , config('constant.DANG_VAN_CHUYEN'), config('constant.HOAN_THANH')])) {
            $entity->status_partner = config('constant.PARTNER_XAC_NHAN');
        } else if ($entity->status == config('constant.HUY')) {
            $entity->status_partner = config('constant.PARTNER_HUY');
        } else if ($entity->status == 8) {
            $entity->status = config('constant.KHOI_TAO');
            $entity->status_partner = config('constant.PARTNER_CHO_GIAO_DOI_TAC_VAN_TAI');
        } else if ($entity->status == 9) {
            $entity->status = config('constant.KHOI_TAO');
            $entity->status_partner = config('constant.PARTNER_CHO_XAC_NHAN');
        } else if ($entity->status == 10) {
            $entity->status = config('constant.KHOI_TAO');
            $entity->status_partner = config('constant.PARTNER_YEU_CAU_SUA');
        }

        return $entity;
    }

    /**
     * @param $entity
     * @param $data
     * @param bool $isEdit
     * @param $orderOld
     */
    public function _processCreateRelation($entity, $data, $isEdit = false, $orderOld = null)
    {
        $isSaveOrder = false;
        //Tính quãng đường DH
        //$this->calcOrderDistance($entity, $entity->location_destination_id, $entity->location_arrival_id);

        //Cập nhật hạn thu chứng từ
        if ($entity->status == config('constant.HOAN_THANH')) {
            if ($isEdit)
                $this->getRepository()->updateDateTimeCollectedDocument($entity->id);
            else {
                $locationEntity = $this->getLocationRepository()->getLocationsById($entity->location_arrival_id);
                if ($locationEntity) {
                    $dateCollected = date('Y-m-d', strtotime($entity->ETA_date_reality . ' + ' . (int)$locationEntity->limited_day . ' days'));
                    $timeCollected = $entity->ETA_time_reality;
                    $entity->date_collected_documents = $dateCollected;
                    $entity->time_collected_documents = $timeCollected;
                    $isSaveOrder = true;
                }
            }
        }

        $currentDay = new DateTime();
        if ($entity->status == config('constant.HUY')) {
            $entity->ETD_date_reality = isset($orderOld) ? ($orderOld->ETD_date_reality ? AppConstant::convertDate($orderOld->ETD_date_reality, 'Y-m-d')
                : $currentDay->format('Y-m-d')) : $currentDay->format('Y-m-d');
            $entity->ETD_time_reality = isset($orderOld) ? $orderOld->ETD_time_reality : $currentDay->format('H:i');
            $entity->ETA_date_reality = $currentDay->format('Y-m-d');
            $entity->ETA_time_reality = $currentDay->format('H:i');
            $isSaveOrder = true;
        }

        if (in_array($entity->status, [config('constant.KHOI_TAO'), config('constant.SAN_SANG')])) {
            $entity->vehicle_id = null;
            $entity->primary_driver_id = null;
            $entity->secondary_driver_id = null;
            $entity->route_id = null;
            $data['route_id'] = null;
            $isSaveOrder = true;
        }

        if ($isSaveOrder)
            $entity->save();

        $this->_saveOrderFile($data, $entity->id);

        // lưu danh bạ
        //$this->saveContact($data);

        $location_destination = isset($entity->locationDestinations) && count($entity->locationDestinations) > 0 ? ($entity->locationDestinations)[0] : null;
        $location_arrival = $entity->locationArrivals && count($entity->locationArrivals) > 0 ? ($entity->locationArrivals)[0] : null;;

        $isSaveDestination = isset($data['auto-create-template_destination']) ? $data['auto-create-template_destination'] : 0;
        $isSaveArrival = isset($data['auto-create-template_arrival']) ? $data['auto-create-template_arrival'] : 0;
        $customerId = empty($data['customer_id']) ? null : $data['customer_id'];

        if ($isSaveDestination != 0 && $location_destination && isset($location_destination['location_id'])) {
            $location_id = $location_destination['location_id'];
            // Check id truyen len: dia diem free text - se chua id
            if (strlen($location_id) > 2 && strpos($location_id, 'id') === 0) {
                $location_id = substr_replace($location_id, '', 0, strlen('id'));
                $location = $this->doLocationInput($location_id);
            } else {
                $location = $this->getLocationRepository()->getLocationsById($location_id);
            }
            $full_address = $location->full_address;
            $location_title = $location->title;
            $phone_number = isset($data['contact_mobile_no_destination']) ? $data['contact_mobile_no_destination'] : "";
            $contact_name = isset($data['contact_name_destination']) ? $data['contact_name_destination'] : "";
            $email = isset($data['contact_email_destination']) ? $data['contact_email_destination'] : "";

            // nếu có tên liên hệ, số điện thoại và địa chỉ thì mới cho phép lưu
            if ($location_id && $phone_number != "" && $contact_name != "") {
                $existContact = $this->getContactRepository()->checkExistPhoneNumberAndName($phone_number, $contact_name);
                if (!$existContact) {
                    $contactEntity = $this->getContactRepository()->findFirstOrNew([]);
                    $contactEntity->location_id = $location_id;
                    $contactEntity->full_address = $full_address;
                    $contactEntity->location_title = $location_title;
                    $contactEntity->phone_number = $phone_number;
                    $contactEntity->contact_name = $contact_name;
                    $contactEntity->email = $email;
                    $contactEntity->customer_id = $customerId;

                    $contactEntity->save();
                }
            }
        }

        if ($isSaveArrival != 0 && $location_arrival && isset($location_arrival['location_id'])) {
            $location_id = $location_arrival['location_id'];
            // Check id truyen len: dia diem free text - se chua id
            if (strlen($location_id) > 2 && strpos($location_id, 'id') === 0) {
                $location_id = substr_replace($location_id, '', 0, strlen('id'));
                $location = $this->doLocationInput($location_id);
            } else {
                $location = $this->getLocationRepository()->getLocationsById($location_id);
            }
            $full_address = $location->full_address;
            $location_title = $location->title;
            $phone_number = isset($data['contact_mobile_no_arrival']) ? $data['contact_mobile_no_arrival'] : "";
            $contact_name = isset($data['contact_name_arrival']) ? $data['contact_name_arrival'] : "";
            $email = isset($data['contact_email_arrival']) ? $data['contact_email_arrival'] : "";


            // nếu có tên liên hệ, số điện thoại và địa chỉ thì mới cho phép lưu
            if ($location_id != "" && $phone_number != "" && $contact_name != "") {
                $existContact = $this->getContactRepository()->checkExistPhoneNumberAndName($phone_number, $contact_name);
                if (!$existContact) {
                    $contactEntity = $this->getContactRepository()->findFirstOrNew([]);
                    $contactEntity->location_id = $location_id;
                    $contactEntity->full_address = $full_address;
                    $contactEntity->location_title = $location_title;
                    $contactEntity->phone_number = $phone_number;
                    $contactEntity->contact_name = $contact_name;
                    $contactEntity->email = $email;
                    $contactEntity->customer_id = $customerId;

                    $contactEntity->save();
                }
            }
        }

        //Notify cho đối tác vận tải
        $partner_id_old = $orderOld ? $orderOld->partner_id : 0;
        $partner_id_new = empty($data['partner_id']) ? 0 : $data['partner_id'];
        if ($partner_id_old != $partner_id_new) {
            // Send notification partner old
            if ($partner_id_old != null && $partner_id_old != 0) {
                $cancelUserIds = $this->getAdminUserRepository()->getPartnerUserForNotifyById($partner_id_old);
                $this->getNotificationService()->notifyC20ToPartner(2, $cancelUserIds, ['order_id' => $entity->id, 'order_code' => $entity->order_code]);
            }

            // Send notification partner new
            if ($partner_id_new != null && $partner_id_new != 0) {
                $assignUserIds = $this->getAdminUserRepository()->getPartnerUserForNotifyById($partner_id_new);
                $this->getNotificationService()->notifyC20ToPartner(1, $assignUserIds, ['order_id' => $entity->id, 'order_code' => $entity->order_code]);
            }
        } else {
            if ($isEdit && $orderOld && $partner_id_new != null && $partner_id_new != 0) {
                $assignUserIds = $this->getAdminUserRepository()->getPartnerUserForNotifyById($partner_id_new);
                $type = 0;

                // Notify đối tác khi đơn hàng bị huỷ

                if (config("constant.HUY") == $entity->status) {
                    $type = 2;
                } elseif ($this->isChangeOrderInfo($entity)) {
                    $type = 3;
                }
                if ($type != 0) {
                    $this->getNotificationService()->notifyC20ToPartner($type, $assignUserIds, ['order_id' => $entity->id, 'order_code' => $entity->order_code]);
                }
            }
        }

        // Notify cho tài xế
        $primary_driver_id_old = $orderOld ? $orderOld->primary_driver_id : 0;
        $primary_driver_id_new = empty($data['primary_driver_id']) ? 0 : $data['primary_driver_id'];
        if ($primary_driver_id_old != $primary_driver_id_new) {
            // Send notification driver old
            if ($primary_driver_id_old != null && $primary_driver_id_old != 0) {
                $cancelUserIds[] = $primary_driver_id_old;
                $this->getNotificationService()->notifyC20OrPartnerToDriver(2, $cancelUserIds, $entity);
            }

            // Send notification driver new
            if ($primary_driver_id_new != null && $primary_driver_id_new != 0) {
                $assignUserIds[] = $primary_driver_id_new;
                $this->getNotificationService()->notifyC20OrPartnerToDriver(1, $assignUserIds, $entity);
            }
        } else {
            if ($isEdit && $orderOld && $primary_driver_id_new != null && $primary_driver_id_new != 0) {
                $assignUserIds[] = $primary_driver_id_new;
                $type = 0;

                if (config("constant.HUY") == $entity->status) {
                    $type = 2;
                } elseif ($this->isChangeOrderInfo($entity)) {
                    $type = 3;
                }
                if ($type != 0) {
                    $this->getNotificationService()->notifyC20OrPartnerToDriver($type, $assignUserIds, $entity);
                }
            }
        }

        // Xử lý chuyến , ĐH ghép ko tạo chuyến
        if ($isEdit || !isset($entity->is_merge_item) || $entity->is_merge_item != config('constant.yes'))
            $this->getRouteService()->_processRouteFromOrder(
                $isEdit ? 2 : 1,
                $entity,
                isset($data['route_id']) ? $data['route_id'] : null,
                $data['vehicle_id'],
                $data['primary_driver_id'],
                $orderOld
            );

        // Xử lý đơn đặt hàng
        $orderCustomer = $this->getOrderCustomerRepository()->getItemById($entity->order_customer_id);
        $this->getOrderCustomerService()->updateOrderCustomerInfo($orderCustomer, true);
    }

    //Kiểm tra thay đổi thông tin giao vận
    public function isChangeOrderInfo($order)
    {
        if ($order->isDirty('ETD_date') || $order->isDirty('ETD_time') || $order->isDirty('ETA_date')
            || $order->isDirty('ETA_time') || $order->isDirty('location_destination_id') || $order->isDirty('location_arrival_id'))
            return true;

        return false;
    }

    public function _deleteRelations($entity)
    {
        $order_id = $entity->id;
        if ($order_id) {
            $this->getOrderHistoryRepository()->deleteWhere([
                'order_id' => $order_id
            ]);
            //delete order_file
            $orderFiles = $this->getOrderFileRepository()->getOrderFileWithOrderID($order_id);
            if ($orderFiles != null) {
                foreach ($orderFiles as $orderFileEntity) {
                    $fileEntity = $this->getFileRepository()->getFileWithID($orderFileEntity->file_id);
                    if ($fileEntity != null) $fileEntity->delete();
                    $orderFileEntity->delete();
                }
            }

            //Xử lý chuyến khi xóa đơn
            $this->getRouteService()->_processRouteFromOrderDelete($entity);

            //Xử lý DHKH
            $orderCustomer = $this->getOrderCustomerRepository()->getItemById($entity->order_customer_id);
            $this->getOrderCustomerService()->updateOrderCustomerInfo($orderCustomer, true);
        }
    }

    protected function _prepareShow($id)
    {
        $isValid = $this->getRepository()->getValidator()->validateShow($id);
        if (!$isValid) {
            return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
        }

        if (!$this->getRepository()->checkRoleOrder([$id])) {
            return HttpCode::EC_UNAUTHENTICATED;
        }

        $order_status_file_list = config("system.order_status_file");
        $order_file_list = [];

        $statuses = array_map(function ($order_status_file) {
            return $order_status_file['id'];
        }, $order_status_file_list);

        $results = $this->getOrderFileRepository()->getFileByOrderIdAndStatus($id, implode(",", $statuses));
        foreach ($order_status_file_list as $order_status_file) {
            foreach ($results as $result) {
                if ($order_status_file['id'] == $result->order_status) {
                    $order_file_list[$order_status_file['id']]['file_id'] = $result->file_ids;
                    $order_file_list[$order_status_file['id']]['reason'] = $result->reason;
                }
            }
        }
        $order_history_list = $this->getOrderHistoryRepository()->getOrderHistoryWithOrderId($id);
        $vehicle = null;
        $primary_driver = null;
        $secondary_driver = null;
        $entity = $this->getRepository()->findWithRelation($id);

        if ($entity->vehicle_id != null) {
            $vehicle = (object)[
                'id' => $entity->vehicle_id,
                'reg_no' => empty($entity->vehicle) ? "" : $entity->vehicle->reg_no
            ];
        }
        if ($entity->primary_driver_id != null) {
            $primary_driver = (object)[
                'id' => $entity->primary_driver_id,
                'full_name' => empty($entity->primaryDriver) ? "" : $entity->primaryDriver->full_name
            ];
        }
        if ($entity->secondary_driver_id != null) {
            $secondary_driver = (object)[
                'id' => $entity->secondary_driver_id,
                'full_name' => empty($entity->secondaryDriver) ? "" : $entity->secondaryDriver->full_name
            ];
        }

        $route = $this->getRoutesRepository()->getItemById($entity->route_id);

        $orderCustomer = $this->getOrderCustomerRepository()->getItemByOrderID($id);

        $this->setViewData([
            'order_status_file_list' => $order_status_file_list,
            'order_file_list' => $order_file_list,
            'order_history_list' => $order_history_list,
            'show_history' => true,
            'route' => $route,
            'order_customer' => $orderCustomer,
            'vehicle' => $vehicle,
            'primary_driver' => $primary_driver,
            'secondary_driver' => $secondary_driver
        ]);

        $locations = $entity->listLocations;
        $entity->locationArrivals = $locations->where('pivot.type', config('constant.ARRIVAL'))->pluck('pivot')->toArray();
        $entity->locationDestinations = $locations->where('pivot.type', config('constant.DESTINATION'))->pluck('pivot')->toArray();

        $this->_prepareDisplay($entity);
        $entity = $this->_processShowListGoods($entity);


        $qrcode = $this->_generateQRCode($entity);

        $this->setViewData([
            'qrcode' => $qrcode,
        ]);
        return $this->setEntity($entity);
    }

    private function _generateQRCode($entity)
    {
        $uuid = uniqid();
        $im = imagecreatetruecolor(180, 30);

        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $grey = imagecolorallocate($im, 128, 128, 128);
        imagefilledrectangle($im, 0, 0, 179, 29, $white);

        $text = $entity->order_code;
        $font = 'fonts/arial.ttf';
        $font_size = 8;
        $angle = 45;
        $text_box = imagettfbbox($font_size, $angle, $font, $text);

        $text_width = $text_box[2] - $text_box[0];
        $text_height = $text_box[7] - $text_box[1];

        $x = (150 / 2) - ($text_width / 2);
        $y = (30 / 2) - ($text_height / 2);

        imagettftext($im, $font_size, 0, $x, $y + 1, $grey, $font, $text);
        imagettftext($im, $font_size, 0, $x, $y, $black, $font, $text);
        imagepng($im, $uuid . "_text.png");
        $id = empty($entity->id) ? $entity->order_id : $entity->id;
        QrCode::format('png')
            ->size(180)
            ->margin(1)
            ->encoding('UTF-8')
            ->errorCorrection('H')
            // ->merge(public_url('favicon.png'), .3)
            ->generate(env("APP_URL") . '|' . $id, $uuid . '_qrcode.png');

        $bottom = imagecreatefrompng($uuid . "_text.png");
        $top = imagecreatefrompng($uuid . "_qrcode.png");

        // get current width/height
        list($top_width, $top_height) = getimagesize($uuid . "_qrcode.png");
        list($bottom_width, $bottom_height) = getimagesize($uuid . "_text.png");

        // compute new width/height
        $new_width = ($top_width > $bottom_width) ? $top_width : $bottom_width;
        $new_height = $top_height + $bottom_height;

        // create new image and merge
        $new = imagecreate($new_width, $new_height);
        imagecopy($new, $top, 0, 0, 0, 0, $top_width, $top_height);
        imagecopy($new, $bottom, 0, $top_height + 1, 0, 0, $bottom_width, $bottom_height);

        // save to file
        imagepng($new, $uuid . "_merge_qrcode.png");
        imagedestroy($im);
        imagedestroy($new);
        $file = base64_encode(file_get_contents($uuid . "_merge_qrcode.png"));

        unlink($uuid . "_merge_qrcode.png");
        unlink($uuid . "_text.png");
        unlink($uuid . "_qrcode.png");

        return $file;
    }

    protected function _prepareDisplay($entity)
    {
        $goodTypeSelected = empty($entity->goods_type) ? '' : $entity->goods_type;
        $goodTypeSelected = is_array($goodTypeSelected) ? $goodTypeSelected : explode(',', $goodTypeSelected);
        $goodTypes = $this->getGoodsTypeRepository()->getListForSelect()->toArray();
        $goodUnits = $this->getGoodsUnitRepository()->getListForSelect()->toArray();
        $currency = $this->getCurrencyRepository()->getListForSelect();
        $goodTypeShow = [];

        foreach ($goodTypeSelected as $item) {
            if (array_key_exists($item, $goodTypes)) {
                $goodTypeShow[] = $goodTypes[$item];
            }
        }

        $locationDestinations = data_get($entity->locationDestinations, '*.location_id');
        $locationArrivals = data_get($entity->locationArrivals, '*.location_id');

        $locations = array_unique(array_merge($locationDestinations, $locationArrivals));

        $orderReviewCustomer = null;
        if ($entity->order_review_id) {
            $orderReviewCustomer = $this->getOrderCustomerReviewRepos()->find($entity->order_review_id);
        }

        $this->setViewData([
            'goodTypeShow' => implode(', ', $goodTypeShow),
            'currency' => isset($entity->currency_id) && isset($currency[$entity->currency_id]) ? $currency[$entity->currency_id] : '',
            'goodsUnits' => $goodUnits,
            'locations' => $this->getLocationRepository()->search(['id_in' => $locations])->pluck('title', 'id')->toArray(),
            'orderReviewCustomer' => $orderReviewCustomer
        ]);
    }

    public function getDataForComboBox()
    {
        try {
            $routeId = Request::get('route_id');
            $vehicleId = \Request::get('vehicle_id');
            $driverId = \Request::get('driver_id');

            $query = $this->getRepository()->getItemsByRouteIDOrVehicleIDOrDriverID($routeId, $vehicleId, $driverId);
            return response()->json(['items' => $query->toArray()['data'], 'pagination' => $query->nextPageUrl() ? true : false]);
        } catch (Exception $e) {
            logError($e);
        }

        return response()->json([]);
    }

    public function getOrderHistory()
    {
        $orderId = \Request::get('order_id', null);

        $order_history_list = $this->getOrderHistoryRepository()->getOrderHistoryWithOrderId($orderId);
        $this->setViewData(['order_history_list' => $order_history_list]);

        $order = $this->getRepository()->getOrderInfoById($orderId);

        $html = [
            'content' => $this->render('backend.order._order_history_list')->render(),
            'orderInfo' => $order
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    public function getOrderRouteMap()
    {
        $orderId = \Request::get('order_id', null);
        $order = $this->getRepository()->getOrderInfoById($orderId);

        $html = [
            'orderInfo' => $order
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    public function _validStatusFollowField($params)
    {
        $status = isset($params['status']) ? $params['status'] : 0;
        if (isset($params['vehicle'])) {
            $params['vehicle_id'] = $params['vehicle'];
        }
        if (isset($params['primary_driver'])) {
            $params['primary_driver_id'] = $params['primary_driver'];
        }

        $value1 = $this->emptyFormInfo($params);
        $value2 = $this->emptyFormVehicle($params);
        if (!$value1 && !$value2) {
            if ($status <= config("constant.SAN_SANG")) {
                return "Vui lòng chọn trong các trạng thái " . config("system.order_status." . config("constant.TAI_XE_XAC_NHAN"))
                    . ', ' . config("system.order_status." . config("constant.CHO_NHAN_HANG")) . ', ' . config("system.order_status." . config("constant.DANG_VAN_CHUYEN"))
                    . ', ' . config("system.order_status." . config("constant.HOAN_THANH")) . ', ' . config("system.order_status." . config("constant.HUY"));
            }
        } /*else {
            if (!$value1) {
                if ($status < config("constant.SAN_SANG")) {
                    return "Vui lòng chọn trạng thái " . config("system.order_status." . config("constant.SAN_SANG"));
                }
            }
        }*/

        return "";
    }

    public function _validStatusDocumentField($params)
    {
        $status = isset($params['status']) ? $params['status'] : 0;
        if (
            $status == config("constant.HOAN_THANH") && $params['is_collected_documents'] == 1
            && $params['status_collected_documents'] != config("constant.DA_THU_DU")
        ) {
            return "Vui lòng chọn trạng thái là đã thu đủ";
        }
        return "";
    }

    public function _validRoute($params)
    {
        $vehicleId = isset($params['vehicle_id']) ? $params['vehicle_id'] : 0;
        $primaryDriverId = isset($params['primary_driver_id']) ? $params['primary_driver_id'] : 0;
        if ($vehicleId && $vehicleId != 0 && $primaryDriverId && $primaryDriverId != 0) {
            if (!empty($params['route_id'])) {
                $route = $this->getRoutesRepository()->getItemById($params['route_id']);
                if ($route) {
                    if ($vehicleId != $route->vehicle_id || $primaryDriverId != $route->driver_id) {
                        return "Xe - tài xế không trùng với xe - tài xế của chuyến đã chọn";
                    }
                }
            }
        }
        return "";
    }

    public function emptyFormInfo($params)
    {
        $order_code = isset($params['order_code']) ? $params['order_code'] : null;
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : null;
        $location_destination_id = isset($params['location_destination_id']) ? $params['location_destination_id'] : null;
        if ($location_destination_id == null)
            $location_destination_id = isset($params['locationDestinations']) && count($params['locationDestinations']) != 0
            && isset($params['locationDestinations'][0]['location_id']) ? $params['locationDestinations'][0]['location_id'] : null;
        $location_arrival_id = isset($params['location_arrival_id']) ? $params['location_arrival_id'] : null;
        if ($location_arrival_id == null)
            $location_arrival_id = isset($params['locationArrivals']) && count($params['locationArrivals']) != 0
            && isset($params['locationArrivals'][0]['location_id']) ? $params['locationArrivals'][0]['location_id'] : null;
        $partner_id = isset($params['partner_id']) ? $params['partner_id'] : null;
        if (
            $order_code == null || $customer_id == null
            || $location_destination_id == null || $location_arrival_id == null
            || $partner_id == null
        )
            return true;
        return false;
    }

    public function emptyFormVehicle($params)
    {
        $vehicle_id = isset($params['vehicle_id']) ? $params['vehicle_id'] : null;
        return $vehicle_id == null;
    }

    public function doLocationInput($address)
    {
        $location = null;
        try {
            $location = $this->getLocationRepository()->findAddress($address);
            if (!isset($location)) {
                DB::beginTransaction();
                $location = $this->getLocationRepository()->findFirstOrNew([]);
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_location'), null, true);
                $location->code = $code;
                $location->title = $address;
                $location->full_address = $address;
                $location->address = $address;
                $location->province_id = '';
                $location->district_id = '';
                $location->ward_id = '';
                $location->address_auto_code = ' -  - ';
                $location->latitude = '';
                $location->longitude = '';
                $location->save();
                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollBack();
            logError($e);
        }
        return $location;
    }

    public function doCustomerInput($cus)
    {
        $customer = null;
        try {
            $customer = $this->getCustomerRepository()->findByFullName($cus);
            if (!isset($customer)) {
                DB::beginTransaction();
                $customer = $this->getCustomerRepository()->findFirstOrNew([]);
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_customer'), null, true);
                $customer->customer_code = $code;
                $customer->active = 1;
                $customer->type = config('constant.CORPORATE_CUSTOMERS');
                $customer->full_name = $cus;
                $customer->save();
                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollBack();
            logError($e);
        }
        return $customer;
    }

    /**
     * @param $entity Order
     */
    public function getSelected($entity)
    {

        $selectedPrimaryDriver = !empty($entity->primary_driver_id) ? $this->getDriverRepository()->getItemById($entity->primary_driver_id) : null;
        $selectedSecondaryDriver = !empty($entity->secondary_driver_id) ? $this->getDriverRepository()->getItemById($entity->secondary_driver_id) : null;
        $selectedVehicle = !empty($entity->vehicle_id) ? $this->getVehicleRepository()->getItemById($entity->vehicle_id) : null;
        $selectedRoute = !empty($entity->route_id) ? $this->getRoutesRepository()->getItemById($entity->route_id) : null;

        $entity->goods = !empty($entity->goods) ? $entity->goods :
            $entity->listGoods->pluck('pivot')->toArray();

        $goodTypeIds = data_get($entity->goods, '*.goods_type_id');
        $goodTypes = $this->getGoodsTypeRepository()->search(['id_in' => $goodTypeIds])->pluck('title', 'id');

        $customer = isset($entity->customer_id) ?
            $this->getCustomerRepository()->search(['id_eq' => $entity->customer_id])->pluck('full_name', 'id')
            : [];

        $locations = $entity->listLocations;
        $locationDestinations = !empty($entity->locationDestinations) ? $entity->locationDestinations :
            $locations->where('pivot.type', config('constant.DESTINATION'))->pluck('pivot')->toArray();
        $locationArrivals = !empty($entity->locationArrivals) ? $entity->locationArrivals :
            $locations->where('pivot.type', config('constant.ARRIVAL'))->pluck('pivot')->toArray();

        if (isset($entity->locationDestinations)) {
            foreach ($entity->locationDestinations as $index => &$location) {
                if ($index == 0) {
                    if ($entity->status == config('constant.HOAN_THANH')) {
                        $entity->ETD_date_reality = $location['date_reality'];
                        $entity->ETD_time_reality = $location['time_reality'];
                    }
                }
            }
        }
        if (isset($entity->locationArrivals)) {
            foreach ($entity->locationArrivals as $index => &$location) {
                if ($index == 0) {
                    if ($entity->status == config('constant.HOAN_THANH')) {
                        $entity->ETA_date_reality = $location['date_reality'];
                        $entity->ETA_time_reality = $location['time_reality'];
                    }
                }
            }
        }

        $locationIds = array_merge(data_get($locationDestinations, '*.location_id'), data_get($locationArrivals, '*.location_id'));
        $locationList = $this->getLocationRepository()->getLocationsByIds(array_unique($locationIds));

        $userAdminList = $this->getAdminUserRepository()->getListForSelect();

        $this->setViewData([
            'locationList' => $locationList,
            'primaryDriver' => $selectedPrimaryDriver,
            'secondaryDriver' => $selectedSecondaryDriver,
            'vehicle' => $selectedVehicle,
            'locationDestinations' => $locationDestinations,
            'locationArrivals' => $locationArrivals,
            'goodsTypes' => $goodTypes,
            'route' => $selectedRoute,
            'customer' => $customer,
            'collected_documents_status_list' => config('system.collected_documents_combo'),
            'userAdminList' => $userAdminList
        ]);
    }

    protected function _processDataImport($update = false)
    {
        $excelColumnConfig = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'order');
        $excelColumnConfigMap = $excelColumnConfig->excelColumnMappingConfigs->pluck('original_field', 'field')->toArray();

        $dataImport = $this->handleDataImport(request(), $excelColumnConfig, $excelColumnConfigMap, $update);
        $data = $dataImport[0];
        $goodsList = $dataImport[1];

        $currentController = $this->getCurrentControllerName();
        $backendExcel = session(self::SESSION_EXCEL, []);
        $backendExcel[$currentController] = $data;
        $backendExcel[$currentController . '_type'] = $update;
        session([self::SESSION_EXCEL => $backendExcel]);

        $this->setViewData([
            'excelColumnMappingConfigs' => $excelColumnConfig->excelColumnMappingConfigs,
            'entities' => $data,
            'goodsList' => $goodsList
        ]);
        return [
            'content' => $this->render('backend.order.import')->render(),
            'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
        ];
    }

    public function handleDataImport($request, $excelColumnConfig, $excelColumnConfigMap, $update = false, $fromEditor = false)
    {
        $data = $fromEditor ? $request->get('data') : json_decode($request->get('data'));
        $dataGoods = $fromEditor ? [] : json_decode($request->get('dataGoods'));
        $vehicleDriverList = $this->getVehicleRepository()->getVehicleAndDriverList();
        $driverList = $this->getDriverRepository()->search()->get();
        $customerList = $this->getCustomerRepository()->search()->get();
        $locationList = $this->getLocationRepository()->search()->get();
        $orderImport = new OrderImport(
            $this->getLocationRepository(),
            $this->getProvinceRepository(),
            $this->getDistrictRepository(),
            $this->getWardRepository(),
            $this->getVehicleRepository(),
            $vehicleDriverList,
            $driverList,
            $customerList,
            $locationList,
            $excelColumnConfigMap,
            $fromEditor
        );

        //Đọc file
        foreach ($data as $key => &$row) {
            $row = $orderImport->map($row, $excelColumnConfig, $data);
        }

        $customerRoleList = $this->getCustomerRepository()->getAllCustomerByRole()->pluck('customer_code', 'id')->toArray();
        $orderUpdates = [];
        if ($update) {
            $orderList = $this->getRepository()->getOrdersByOrderCodes(array_column($data, 'order_code'));
            foreach ($orderList as $item) {
                $orderUpdates[$item->order_code] = $item;
            }
        }

        foreach ($data as $key => &$row) {

            $row['importable'] = true;
            $row['failures'] = [];
            $row['warning'] = [];
            $row['error'] = [];

            if ($update) {
                if (isset($orderUpdates[$row['order_code']])) {
                    $orderEntity = $orderUpdates[$row['order_code']];

                    if (!$orderEntity) {
                        $row['failures'][] = 'Không tìm thấy mã đơn hàng';
                    } else {
                        $row['id'] = $orderEntity->id;
                        $fields = $orderEntity->getFillable();
                        foreach ($fields as $fieldName) {
                            $row['current_' . $fieldName] = $orderEntity->$fieldName;
                        }

                        if ($orderEntity->is_lock == 1) {
                            $row['failures'][] = 'Đơn hàng đang được khoá. Bạn không được phép cập nhật';
                            $row['error']['order_code'] = 'Đơn hàng đang được khoá. Bạn không được phép cập nhật';
                        }
                    }
                }
            }

            //Nếu nhập free text thì ko check
            if (!$fromEditor && !empty($row['customer_id']) && !isset($customerRoleList[$row['customer_id']])) {
                $row['importable'] = false;
                $row['failures'][] = 'Mã khách hàng không khớp với trên hệ thống hoặc không có quyền quản lý khách hàng.';
            }

            $errorStatus = $this->_validStatusFollowField($row);
            if (!empty($errorStatus)) {
                $row['importable'] = false;
                $row['failures'][] = $errorStatus;
                $row['error']['status'] = $errorStatus;
            }

            if (!empty($row['vehicle']) && !empty($row['primary_driver']) && $row['status'] == config('constant.SAN_SANG')) {
                $row['status'] = config('constant.TAI_XE_XAC_NHAN');
            }
        }

        $goodsList = array();
        $data_goods = array();
        foreach ($dataGoods as $key => $rowGoods) {
            if ($key == 0) {
                $goodsList = $orderImport->mapRowGoodsCode($rowGoods);
                continue;
            }
            $result = $orderImport->mapGoods($goodsList, $rowGoods);
            if (!empty($result)) {
                $data_goods[$result[0]] = $result[1];
            }
        }

        $data = $orderImport->processLocationImport($data);
        $data = $orderImport->processGoodImport($data, $data_goods);

        if ($update == true) {
            $this->getRepository()->getValidator()->validateImportUpdate($data, $fromEditor);
        } else {
            $this->getRepository()->getValidator()->validateImport($data, $fromEditor);
        }
        $errors = $this->getRepository()->getValidator()->errorsBag();

        $data = $orderImport->validRouteExcel($data, $update);

        $data = $orderImport->validOrderCustomerExcel($data);

        foreach ($data as $key => &$row) {
            if (!empty($errors))
                foreach ($errors->get($key . '.*') as $attribute => $message) {
                    $row['failures'][] = Arr::get($message, 0);
                    if ($fromEditor)
                        $row['error'][str_replace($key . '.', '', $attribute)] = Arr::get($message, 0);
                }

            //Xử lý thêm KH nếu nhập free text
            if ($row["status"] != config("constant.KHOI_TAO") && (empty($row['customer_id']) || ($row['customer_id'] == 0))
                && empty($row['customer_code'])) {
                $row['failures'][] = "Khách hàng là bắt buộc";
            }

            $customerNews = []; // Lưu KH mới tạo
            if (!$fromEditor && empty($row['customer_id']) && !empty($row['customer_code'])) {
                if (!isset($customerNews[$row['customer_id']])) {
                    $customerEntity = $this->doCustomerInput($row['customer_code']);
                    $customerNews[$customerEntity->id] = $customerEntity;
                    $row['customer_id'] = $customerEntity->id;
                } else {
                    $customerEntity = $customerNews[$row['customer_id']];
                    $row['customer_id'] = $customerEntity->id;
                }
            }

            foreach ($row['order_locations'] as $i => &$location) {

                if (!$fromEditor && empty($location['location_destination_id']) && !empty($location['location_destination_code'])) {
                    $destinationOutput = $this->generalLocationFromExcel($orderImport, $location['location_destination_code']);
                    if (!empty($destinationOutput)) {
                        $errorMessage = $destinationOutput[0];
                        $warningMessage = $destinationOutput[1];
                        $locationDestination = $destinationOutput[2];

                        if (empty($errorMessage)) {
                            $location['location_destination_id'] = $locationDestination->id;
                            $location['name_of_location_destination_code'] = $locationDestination->full_address;
                            $location['location_destination_title'] = $locationDestination->title;

                            if (empty($locationDestination->province_id) && empty($locationDestination->district_id)
                                && empty($locationDestination->ward_id))
                                $row['warning'][] = 'Điểm nhận hàng chưa nhập tọa độ';
                        } else {
                            $row['failures'][] = $errorMessage;
                        }
                        if (!empty($warningMessage)) {
                            $row['warning'][] = $warningMessage;
                        }
                    }
                }

                if (!$fromEditor && empty($location['location_arrival_id']) && !empty($location['location_arrival_code'])) {
                    $arrivalOutput = $this->generalLocationFromExcel($orderImport, $location['location_arrival_code']);
                    if (!empty($arrivalOutput)) {
                        $errorMessage = $arrivalOutput[0];
                        $warningMessage = $arrivalOutput[1];
                        $locationArrival = $arrivalOutput[2];

                        if (empty($errorMessage)) {
                            $location['location_arrival_id'] = $locationArrival->id;
                            $location['name_of_location_arrival_code'] = $locationArrival->full_address;
                            $location['location_arrival_title'] = $locationArrival->title;

                            if (empty($locationArrival->province_id) && empty($locationArrival->district_id)
                                && empty($locationArrival->ward_id))
                                $row['warning'][] = 'Điểm trả hàng chưa nhập tọa độ';
                        } else {
                            $row['failures'][] = $errorMessage;
                        }
                        if (!empty($warningMessage)) {
                            $row['warning'][] = $warningMessage;
                        }
                    }
                }

                if ($i == 0) {
                    $row['location_destination_id'] = $location['location_destination_id'];
                    $row['location_destination_title'] = $location['location_destination_title'];
                    $row['location_arrival_id'] = $location['location_arrival_id'];
                    $row['location_arrival_title'] = $location['location_arrival_title'];
                    $row['location_arrival_limited_day'] = isset($location['location_arrival_limited_day']) ? $location['location_arrival_limited_day'] : null;
                }
            }

            if (empty($row['failures'])) continue;
            $row['importable'] = false;
        }

        return [$data, $goodsList];
    }

    protected function _processFileImport()
    {
        $backendExcel = session(self::SESSION_EXCEL, array());
        $currentController = $this->getCurrentControllerName();
        $dataList = $backendExcel[$currentController];
        $update = $backendExcel[$currentController . '_type'];

        $total = count($dataList);
        $ignoreCount = $this->handleFileImport($dataList, $update);

        $file = request()->file;
        if (!empty($file)) {
            app('App\Http\Controllers\Backend\FileController')->uploadImportFile($file, $total, $ignoreCount, $update, $this->getTitle());
        }

        unset($backendExcel[$currentController]);
        unset($backendExcel[$currentController . '_type']);
        unset($backendExcel['routes']);
        session([self::SESSION_EXCEL => $backendExcel]);

        $this->setViewData([
            'total' => $total,
            'done' => $total - $ignoreCount,
        ]);

        return [
            'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
            'content' => $this->render('layouts.backend.elements.excel._import_result_done')->render(),
        ];
    }

    public function handleFileImport($dataList, $update = false, $fromEditor = false)
    {
        $ignoreCount = 0;
        $total = count($dataList);

        $orderEntity = new Order();
        $lastId = 0;
        if (!$update && $total > 0) {
            $lastId = $orderEntity->getAutoIncrementId($total);
            if ($lastId < 0)
                return $total;
        }

        $orderRoute = [];
        $routeNewList = [];
        $routeIdUpdates = [];
        $orderCurrentList = [];

        // Xử lý chuyến từ excel
        if ($update) {
            $routeOut = app('App\Http\Controllers\Backend\RouteController')->_processRouteFromExcel(2, $dataList, $fromEditor);
        } else {
            $routeOut = app('App\Http\Controllers\Backend\RouteController')->_processRouteFromExcel(1, $dataList, $fromEditor);
        }
        $routeNewList = $routeOut[0];
        $orderRoute = $routeOut[1];

        $goodTypes = $this->getGoodsTypeRepository()->search()->get()->keyBy('code');
        $adminUsers = $this->getAdminUserRepository()->search()->pluck('id', 'username');

        $orderList = [];
        $orderGoods = [];
        $orderLocations = [];
        $orderPayments = [];

        $relationField = ['customer_id', 'vehicle_id', 'primary_driver_id', 'secondary_driver_id', 'location_arrival_id', 'location_destination_id'];
        foreach ($dataList as $i => &$data) {
            try {
                if (!$data['importable']) {
                    $ignoreCount++;
                    continue;
                }

                $order = [];
                $orderCurrent = [];

                $fields = $orderEntity->getFillable();
                foreach ($fields as $fieldName) {
                    if ($update) {
                        if (isset($data[$fieldName])) {
                            if (in_array($fieldName, $relationField) && $data[$fieldName] == "0") {
                                $order[$fieldName] = null;
                            } else {
                                $order[$fieldName] = $data[$fieldName];
                            }
                        }
                    } else {
                        if (array_key_exists($fieldName, $data))
                            $order[$fieldName] = $data[$fieldName];
                        else
                            $order[$fieldName] = null;
                    }

                    if (isset($data['current_' . $fieldName]))
                        $orderCurrent[$fieldName] = $data['current_' . $fieldName];
                }

                if (!$update) {
                    $order['id'] = $lastId;
                    $order['ins_id'] = $this->getCurrentUser()->id;
                    unset($order['upd_id']);
                    unset($order['upd_date']);
                    $lastId++;
                } else {
                    $order['id'] = $data['id'];
                    $order['upd_id'] = $this->getCurrentUser()->id;
                    unset($order['ins_id']);
                    unset($order['ins_date']);
                }
                $order['del_flag'] = "0";
                $order['gps_distance'] = isset($order['gps_distance']) ? $order['gps_distance'] : 0;
                $order['customer_id'] = isset($order['customer_id']) ? $order['customer_id'] : 0;

                if ($update) {
                    //Lấy thông tin DH trc update
                    $orderCurrent['id'] = $data['id'];
                    $orderCurrentList[$data['id']] = $orderCurrent;
                    //Lấy ds chuyến cũ trc update
                    $routeIdUpdates[] = $data['current_route_id'];
                    if ($order['status'] == config('constant.SAN_SANG') || $order['status'] == config('constant.KHOI_TAO')) {
                        $order['vehicle_id'] = null;
                        $order['primary_driver_id'] = null;
                        $order['secondary_driver_id'] = null;
                    }
                }

                if ($orderRoute && array_key_exists($data['order_code'], $orderRoute)) {
                    $order['route_id'] = $orderRoute[$data['order_code']];
                }

                $order = OrderImport::processInputExcel($order, $data);

                $order['source_create'] = config("constant.SOURCE_CREATE_ORDER_EXCEL");

                $totalVolume = 0;
                $totalWeight = 0;

                //Luu order good
                foreach ($data['order_goods'] as $code => $quantity) {
                    if (!empty($code) && !empty($quantity) && $quantity > 0) {
                        $codes = explode('|', $code);
                        $goodTypeEntity = $goodTypes->get(trim($codes[0]));
                        if (isset($goodTypeEntity)) {
                            $volume = $goodTypeEntity->volume ? $goodTypeEntity->volume * $quantity : 0;
                            $weight = $goodTypeEntity->weight ? $goodTypeEntity->weight * $quantity : 0;
                            $totalVolume += $volume;
                            $totalWeight += $weight;
                            $orderGoods[] = [
                                'order_id' => $order['id'],
                                'goods_type_id' => $goodTypeEntity->id,
                                'quantity' => $quantity,
                                'goods_unit_id' => $goodTypeEntity->goods_unit_id,
                                'insured_goods' => 0,
                                'volume' => $goodTypeEntity->volume ? $goodTypeEntity->volume : 0,
                                'weight' => $goodTypeEntity->weight ? $goodTypeEntity->weight : 0,
                                'total_volume' => $volume,
                                'total_weight' => $weight,
                                'note' => null,
                            ];
                        }
                    }
                }

                // Nếu excel có nhập tổng thể tích, tổng khối lượng thì ưu tiên ko tính theo số lượng hàng hóa nhập vào
                $order['volume'] = $order['volume'] && $order['volume'] > 0 ? $order['volume'] : $totalVolume;
                $order['weight'] = $order['weight'] && $order['weight'] > 0 ? $order['weight'] : $totalWeight;

                //Lưu order location
                foreach ($data['order_locations'] as $index => $location) {

                    if (!empty($location['location_destination_id'])) {
                        $orderLocations[] = [
                            'order_id' => $order['id'],
                            'location_id' => $location['location_destination_id'],
                            'type' => config('constant.DESTINATION'),
                            'date' => empty($location['ETD_date']) ? null : AppConstant::convertDate($location['ETD_date'], 'Y-m-d'),
                            'date_reality' => empty($location['ETD_date_reality']) ? null : AppConstant::convertDate($location['ETD_date_reality'], 'Y-m-d'),
                            'time' => $location['ETD_time'],
                            'time_reality' => $location['ETD_time_reality'],
                            'note' => $location['informative_destination']
                        ];
                    }
                    if (!empty($location['location_arrival_id'])) {
                        $orderLocations[] = [
                            'order_id' => $order['id'],
                            'location_id' => $location['location_arrival_id'],
                            'type' => config('constant.ARRIVAL'),
                            'date' => empty($location['ETA_date']) ? null : AppConstant::convertDate($location['ETA_date'], 'Y-m-d'),
                            'date_reality' => empty($location['ETA_date_reality']) ? null : AppConstant::convertDate($location['ETA_date_reality'], 'Y-m-d'),
                            'time' => $location['ETA_time'],
                            'time_reality' => $location['ETA_time_reality'],
                            'note' => $location['informative_arrival']
                        ];
                    }
                }

                //Luu thong tin thanh toan
                $orderPayments[] = [
                    'order_id' => $order['id'],
                    'payment_type' => isset($data['payment_type']) ? $data['payment_type'] : null,
                    'payment_user_id' => isset($data['payment_user_id']) && isset($adminUsers[$data['payment_user_id']]) ? $adminUsers[$data['payment_user_id']] : null,
                    'goods_amount' => isset($data['goods_amount']) ? $data['goods_amount'] : null,
                    'vat' => isset($data['vat']) ? $data['vat'] : null,
                    'anonymous_amount' => isset($data['anonymous_amount']) ? $data['anonymous_amount'] : null
                ];

                $orderList[] = $order;

            } catch (Exception $e) {
                $ignoreCount++;
                logError($e . ' - Order : ' . json_encode($data));
            }
        }

        try {
            DB::beginTransaction();

            if ($update) {

                //Tạo chuyến mới cho DH thay đổi xe-tx hoặc ghép lại chuyến
                if (!empty($routeNewList)) {
                    Routes::insert($routeNewList);
                }
                //Cập nhật DH
                if (!empty($orderList)) {
                    $orderInstance = new Order();
                    Batch::update($orderInstance, $orderList, 'id');
                }

                //Cập nhật relation DH
                if (!empty($orderGoods)) {
                    DB::table('order_goods')->whereIn('order_id', array_column($orderList, 'id'))->delete();
                    OrderGood::insert($orderGoods);
                }
                if (!empty($orderLocations)) {
                    DB::table('order_locations')->whereIn('order_id', array_column($orderList, 'id'))->delete();
                    OrderLocation::insert($orderLocations);
                }
                if (!empty($orderPayments)) {
                    DB::table('order_payment')->whereIn('order_id', array_column($orderList, 'id'))->delete();
                    OrderPayment::insert($orderPayments);
                }

            } else {
                if (!empty($routeNewList)) {
                    Routes::insert($routeNewList);
                }
                if (!empty($orderList)) {
                    Order::insert($orderList);
                }
                if (!empty($orderGoods)) {
                    OrderGood::insert($orderGoods);
                }
                if (!empty($orderLocations)) {
                    OrderLocation::insert($orderLocations);
                }
                if (!empty($orderPayments)) {
                    OrderPayment::insert($orderPayments);
                }
            }

            if ($routeNewList)
                foreach ($routeNewList as $route)
                    $routeIdUpdates[] = $route['id'];

            AppConstant::event(new OrderExcelEvent($update, $orderList, $orderCurrentList, $routeIdUpdates, $this->getCurrentUser()->id));

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $ignoreCount = $total;
            logError($e);
        }

        return $ignoreCount;
    }

    public function generalLocationFromExcel(OrderImport $orderImport, $location)
    {
        $locations = explode(",", $location);
        $size = count($locations);
        // TODO: Tạm fix, nếu nhập dữ liệu địa chỉ ko đúng định dạng thì sẽ tạo địa chỉ mới
        if ($size < 4) {
            $locationEntity = $this->doLocationInput($location);
            return ['', '', $locationEntity];
        } else {
            $output = $orderImport->doLocationExcelWithFormat($locations, $location);
            if (empty($output[0])) {
                return $output;
            } else {
                $locationEntity = $this->doLocationInput($location);
                return ['', '', $locationEntity];
            }
        }

    }

    public function getExpiredItem()
    {
        try {
            return $this->getRepository()->getExpiredItem();
        } catch (Exception $e) {
            logError($e);
            return null;
        }
    }

    public function exportReportOrderTemplate(Request $request)
    {
        try {
            $validation = \Illuminate\Support\Facades\Validator::make($request->all(), []);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $orderExport = new OrdersExport(
                    $this->getRepository(),
                    $this->getCustomerRepository(),
                    $this->getGoodsTypeRepository(),
                    $this->getDriverRepository(),
                    $this->getVehicleRepository(),
                    $this->getGoodsUnitRepository(),
                    $this->getLocationRepository(),
                    $this->getReceiptPaymentRepository(),
                    $this->getOrderCustomerRepository(),
                    $this->getAdminUserRepository(),
                    []
                );
                return $orderExport->exportReportTemplate($request);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    public function exportTemplate()
    {
        $currentUser = $this->getCurrentUser();
        if (null == $currentUser || !$currentUser) {
            // Lay account admin ra de lam du lieu default
            $currentUser = $this->getAdminUserRepository()->getAdminUserByUserName('admin');
        }

        $orderExport = new OrdersExport(
            $this->getRepository(),
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
        return $orderExport->exportFileTemplate($currentUser->id);
    }

    public function exportUpdate()
    {
        $currentUser = $this->getCurrentUser();
        $ids = Request::get('ids', null);
        $data = $this->_getDataIndex(false);

        if (isset($ids)) {
            $sort_field = array_key_exists("sort_field", $data) ? $data["sort_field"] : 'orders.id';
            $sort_type = array_key_exists("sort_type", $data) ? $data["sort_type"] : 'desc';
            $data = [];
            $data['id_in'] = explode(',', $ids);
            $data["sort_field"] = $sort_field;
            $data["sort_type"] = $sort_type;
        }

        $orderExport = new OrdersExport(
            $this->getRepository(),
            $this->getCustomerRepository(),
            $this->getGoodsTypeRepository(),
            $this->getDriverRepository(),
            $this->getVehicleRepository(),
            $this->getGoodsUnitRepository(),
            $this->getLocationRepository(),
            $this->getReceiptPaymentRepository(),
            $this->getOrderCustomerRepository(),
            $this->getAdminUserRepository(),
            $data
        );
        $orderExport->is_update = true;
        $orderExport->excelColumnConfig = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'order');
        return $orderExport->exportFileTemplate($currentUser->id);
    }

    public function suggestionLocation()
    {
        $locationId = Request::get('id', null);
        $customerId = Request::get('customer_id', null);
        $contact = $this->getContactRepository()->search([
            'location_id_eq' => $locationId,
            'customer_id_eq' => $customerId,
        ])->first();

        if (empty($contact)) {
            $contact = $this->getContactRepository()->search(['location_id_eq' => $locationId])->first();
        }

        $this->setData([
            'contact' => $contact,
        ]);
        return $this->renderJson();
    }

    /**
     * @param $entity
     * @param string $action
     * @return bool|void
     */
    protected function _saveRelations($entity, $action = 'save')
    {
        parent::_saveRelations($entity, $action);
        $this->_saveGoods($entity);
        $this->_saveLocations($entity);
    }

    protected function _processShowListGoods($entity)
    {
        $listGoods = $entity->listGoods;
        $result = [];

        foreach ($listGoods as $goods) {
            if (empty($goods->pivot)) {
                continue;
            }

            $result[] = [
                'order_goods_id' => $goods->pivot->id,
                'order_id' => $goods->pivot->order_id,
                'goods_type_id' => $goods->id,
                'goods_type' => $goods->title,
                'quantity' => $goods->pivot->quantity,
                'goods_unit_id' => $goods->pivot->goods_unit_id,
                'insured_goods' => $goods->pivot->insured_goods,
                'weight' => $goods->pivot->weight,
                'volume' => $goods->pivot->volume,
                'total_weight' => $goods->pivot->total_weight,
                'total_volume' => $goods->pivot->total_volume,
                'note' => $goods->pivot->note,
            ];
        }
        $entity->goods = $result;

        return $entity;
    }

    /**
     * Lưu thông tin nhiều hàng hóa
     * @param $entity
     */
    protected function _saveGoods($entity)
    {
        $goods = $entity->goods;

        if (empty($goods)) {
            return;
        }
        $data = [];
        foreach ($goods as $goodsItem) {
            $data[] = [
                'goods_type_id' => !empty($goodsItem['goods_type_id']) ? $goodsItem['goods_type_id'] : 0,
                'quantity' => !empty($goodsItem['quantity']) ? $goodsItem['quantity'] : 1,
                'goods_unit_id' => !empty($goodsItem['goods_unit_id']) ? $goodsItem['goods_unit_id'] : 0,
                'insured_goods' => !empty($goodsItem['insured_goods']) ? $goodsItem['insured_goods'] : 0,
                'volume' => !empty($goodsItem['volume']) ? $goodsItem['volume'] : 0,
                'weight' => !empty($goodsItem['weight']) ? $goodsItem['weight'] : 0,
                'note' => !empty($goodsItem['note']) ? $goodsItem['note'] : '',
                'total_weight' => !empty($goodsItem['total_weight']) ? $goodsItem['total_weight'] : 0,
                'total_volume' => !empty($goodsItem['total_volume']) ? $goodsItem['total_volume'] : 0,
            ];
        }
        $entity->listGoods()->detach();
        $entity->listGoods()->sync($data);
    }

    /**
     * Lưu thông tin nhiều địa điểm
     * @param $entity
     */
    protected function _saveLocations($entity)
    {
        $locationDestinations = $entity->locationDestinations;
        $locationArrivals = $entity->locationArrivals;

        if (empty($locationDestinations) && empty($locationArrivals)) {
            return;
        }
        $data = [];
        foreach ($locationDestinations as $index => $locationDestination) {
            if (!empty($locationDestination['location_id']) && !is_numeric($locationDestination['location_id'])) {
                if (strlen($locationDestination['location_id']) > 2 && strpos($locationDestination['location_id'], 'id') === 0) {
                    $locationDestination['location_id'] = substr_replace($locationDestination['location_id'], '', 0, strlen('id'));
                }
                $newLocations = $this->doLocationInput($locationDestination['location_id']);
                $locationDestination['location_id'] = $newLocations->id;
            }
            if ($index == 0) {
                $entity->location_destination_id = !empty($locationDestination['location_id']) ? $locationDestination['location_id'] : 0;
                $entity->ETD_date = !empty($locationDestination['date']) ? format($locationDestination['date'], 'Y-m-d', 'd-m-Y') : null;
                $entity->ETD_time = !empty($locationDestination['time']) ? $locationDestination['time'] : null;
                if ($entity->status == config('constant.HOAN_THANH')) {
                    $entity->ETD_date_reality = !empty($locationDestination['date_reality']) ? format($locationDestination['date_reality'], 'Y-m-d', 'd-m-Y') : null;
                    $entity->ETD_time_reality = !empty($locationDestination['time_reality']) ? $locationDestination['time_reality'] : null;
                }
            }

            if (empty($locationDestination['location_id'])) continue;

            $data[] = [
                'order_id' => $entity->id,
                'location_id' => !empty($locationDestination['location_id']) ? $locationDestination['location_id'] : 0,
                'type' => config('constant.DESTINATION'),
                'date' => !empty($locationDestination['date']) ?
                    format($locationDestination['date'], 'Y-m-d', 'd-m-Y') : null,
                'date_reality' => !empty($locationDestination['date_reality']) ?
                    format($locationDestination['date_reality'], 'Y-m-d', 'd-m-Y') : null,
                'time' => !empty($locationDestination['time']) ? $locationDestination['time'] : null,
                'time_reality' => !empty($locationDestination['time_reality']) ? $locationDestination['time_reality'] : null,
                'note' => !empty($locationDestination['note']) ? $locationDestination['note'] : '',
            ];
        }

        foreach ($locationArrivals as $index => $locationArrival) {
            if (!empty($locationArrival['location_id']) && !is_numeric($locationArrival['location_id'])) {
                if (strlen($locationArrival['location_id']) > 2 && strpos($locationArrival['location_id'], 'id') === 0) {
                    $locationArrival['location_id'] = substr_replace($locationArrival['location_id'], '', 0, strlen('id'));
                }
                $newLocation = $this->doLocationInput($locationArrival['location_id']);
                $locationArrival['location_id'] = $newLocation->id;
            }

            if ($index == 0) {
                $entity->location_arrival_id = !empty($locationArrival['location_id']) ? $locationArrival['location_id'] : 0;
                $entity->ETA_date = !empty($locationArrival['date']) ? format($locationArrival['date'], 'Y-m-d', 'd-m-Y') : null;
                $entity->ETA_time = !empty($locationArrival['time']) ? $locationArrival['time'] : null;
                if ($entity->status == config('constant.HOAN_THANH')) {
                    $entity->ETA_date_reality = !empty($locationArrival['date_reality']) ? format($locationArrival['date_reality'], 'Y-m-d', 'd-m-Y') : null;
                    $entity->ETA_time_reality = !empty($locationArrival['time_reality']) ? $locationArrival['time_reality'] : null;
                }
            }

            if (empty($locationArrival['location_id'])) continue;

            $data[] = [
                'order_id' => $entity->id,
                'location_id' => !empty($locationArrival['location_id']) ? $locationArrival['location_id'] : 0,
                'type' => config('constant.ARRIVAL'),
                'date' => !empty($locationArrival['date']) ?
                    format($locationArrival['date'], 'Y-m-d', 'd-m-Y') : null,
                'date_reality' => !empty($locationArrival['date_reality']) ?
                    format($locationArrival['date_reality'], 'Y-m-d', 'd-m-Y') : null,
                'time' => !empty($locationArrival['time']) ? $locationArrival['time'] : null,
                'time_reality' => !empty($locationArrival['time_reality']) ? $locationArrival['time_reality'] : null,
                'note' => !empty($locationArrival['note']) ? $locationArrival['note'] : '',
            ];
        }

        $entity->listLocations()->detach();
        $entity->listLocations()->sync($data);
        $entity->save();
    }

    protected function _getParams()
    {
        $data = Request::all();
        $listConvert = [
            'loading_destination_fee',
            'loading_arrival_fee',
            'amount',
            'commission_amount',
            'commission_value',
            'quantity',
            'volume',
            'weight',
            'total_volume',
            'total_weight',
            'cod_amount',
            'quantity_order_customer',
            'volume_order_customer',
            'weight_order_customer',
        ];

        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                continue;
            }

            if (in_array($key, $listConvert)) {
                $value = convertNumber($value);
            }
        }
        if (isset($data['goods'])) {
            foreach ($data['goods'] as &$goods) {
                foreach ($goods as $key => &$value) {
                    if (in_array($key, $listConvert)) {
                        $value = convertNumber($value);
                    }
                }
            }
        }
        if (isset($data['orderPayment']['goods_amount'])) {
            $data['orderPayment']['goods_amount'] = convertNumber($data['orderPayment']['goods_amount']);
        }
        if (isset($data['orderPayment']['anonymous_amount'])) {
            $data['orderPayment']['anonymous_amount'] = convertNumber($data['orderPayment']['anonymous_amount']);
        }

        return $data;
    }

    /**
     * @param $action 1- Thêm mới , 2-Sửa
     * @param $order
     * @param $vehicleId
     * @param $primaryDriverId
     * @param $routeId
     * @param null $partnerId
     */
    //Xử lý đơn trên form chuyến : Gán xe và tài xế của chuyến cho đơn , bắn notify cho tài xế
    public function _processOrderFromRoute($action, $order, $vehicleId, $primaryDriverId, $routeId, $partnerId = null)
    {
        $primaryDriverOldId = $order->primary_driver_id;
        $secondaryDriverOldId = $order->secondary_driver_id;

        //Giữ trạng thái đơn nếu đơn trong tình trạng vận chuyển,hoàn thành, hủy
        if (in_array($order->status, [config("constant.SAN_SANG"), config("constant.KHOI_TAO")])) {
            $order->status = config("constant.TAI_XE_XAC_NHAN");
        }
        $order->status_partner = config("constant.PARTNER_XAC_NHAN");

        if ($partnerId)
            $order->partner_id = $partnerId;

        $order->vehicle_id = $vehicleId;
        $order->primary_driver_id = $primaryDriverId;
        $order->route_id = $routeId;
        $order = $this->_processInputData($order);
        $order->save();

        if ($primaryDriverOldId != $primaryDriverId) {

            // Send notification driver old
            if ($primaryDriverOldId != null && $primaryDriverOldId != 0) {
                $cancelUserIds[] = $primaryDriverOldId;
                $this->getNotificationService()->notifyC20OrPartnerToDriver(2, $cancelUserIds, $order);
            }

            // Send notification driver new
            if ($primaryDriverId != null && $primaryDriverId != 0) {
                $assignUserIds[] = $primaryDriverId;
                $this->getNotificationService()->notifyC20OrPartnerToDriver(1, $assignUserIds, $order);
            }
        }
    }

    //Xử lý đơn khi xóa chuyến : Xóa xe-tài xế của đơn, bắn notify về tài xế
    public function _processOrderFromRouteDelete($order)
    {
        $primaryDriverOldId = $order->primary_driver_id;
        $order->status = config("constant.SAN_SANG");
        $order->vehicle_id = 0;
        $order->primary_driver_id = 0;
        $order->secondary_driver_id = 0;
        $order->route_id = null;
        $order = $this->_processInputData($order);
        $order->save();

        if ($primaryDriverOldId && $primaryDriverOldId != 0) {
            $cancelUserIds[] = $primaryDriverOldId;
            $this->getNotificationService()->notifyC20OrPartnerToDriver(2, $cancelUserIds, $order);
        }
    }

    // Xử lý lưu nhanh
    // CreatedBy nlhoang 13/02/2020
    protected function _processQuickSave($id, $field, $value)
    {
        $entity = $this->getRepository()->getItemById($id);
        if ($entity != null) {
            $entity->$field = $value;
            switch ($field) {
                case ('amount'):
                    switch ($entity->commission_type) {
                        case 1:
                            $entity->commission_amount = $entity->commission_value * $entity->amount;
                            break;
                        case 2:
                            break;
                    }
                    break;
                case ('commission_amount'):
                    switch ($entity->commission_type) {
                        case 1:
                            $entity->commission_value = $entity->commission_amount != 0 ? ($entity->commission_amount / $entity->amount) * 100 : 0;
                            break;
                        case 2:
                            $entity->commission_value = $entity->commission_amount;
                            break;
                    }
                    break;
            }

            $entity = $this->_processInputData($entity);
            $entity->save();
        }
    }

    // Cập nhật trạng thái chứng từ
    // CreatedBy nlhoang 13/02/2020
    protected function updateDocuments()
    {
        try {
            $ids = explode(",", request()->get('ids'));
            $time_collected_documents_reality = request()->get('time_collected_documents_reality');
            $date_collected_documents_reality = request()->get('date_collected_documents_reality');

            $currentDay = new DateTime();
            foreach ($ids as $id) {
                $entity = $this->getRepository()->getItemById($id);
                if ($entity != null) {
                    $entity->status_collected_documents = config('constant.DA_THU_DU');

                    $date_collected_documents_reality = empty($date_collected_documents_reality) ? $currentDay->format('Y-m-d')
                        : AppConstant::convertDate($date_collected_documents_reality, 'Y-m-d');
                    $time_collected_documents_reality = empty($time_collected_documents_reality) ? $currentDay->format('H:i')
                        : $time_collected_documents_reality;

                    $entity->date_collected_documents_reality = $date_collected_documents_reality;
                    $entity->time_collected_documents_reality = $time_collected_documents_reality;
                    $entity = $this->_processInputData($entity);
                    $entity->save();
                }
            }
            $data = [
                'ok' => true
            ];
        } catch (Exception $e) {
            logError($e);
            $data = [
                'ok' => false,
                'message' => $e
            ];
        }
        return json_encode($data);
    }


    //Xuất biểu mẫu custom
    //CreatedBy nlhoang 4/4/2020
    public function exportCustomTemplate()
    {
        $orderIds = Request::get('ids');
        $templateId = Request::get('templateId');

        $arr = explode(",", $orderIds);
        $datas = [];
        $template = $this->getTemplateRepository()->getTemplateByTemplateId($templateId);

        foreach ($arr as $item) {
            $data = $this->getRepository()->getExportByID($item, $template);
            $datas[] = [
                'id' => $item,
                'name' => $data->{'order_code'},
                'data' => $data
            ];
        }

        $orderExport = new TemplateExport(
            $this->getTemplateRepository(),
            $datas
        );
        return $orderExport->exportCustomTemplate($templateId);
    }

    // Hỗ trợ In file vận đơn từ url đơn hàng
    protected function printBillFromUrl()
    {
        try {
            $ids = explode(",", request()->get('ids'));
            $apiConfig = $this->getTpApiRepos()->getApiConfig(config('constant.3P_1MG'));
            $client = new \GuzzleHttp\Client(['headers' => [$apiConfig->request_header_authen => $apiConfig->access_token]]);
            $results = [];
            foreach ($ids as $id) {
                $entity = $this->getRepository()->getItemById($id);
                if ($entity != null && !empty($entity->bill_print_url)) {
                    $request = $client->get($entity->bill_print_url);
                    $response = $request->getBody();
                    if ($response != null) {
                        $content = $response->getContents();
                        $item = new \stdClass();
                        $item->name = $entity->order_code;
                        $item->data = base64_encode($content);
                        $results[] = $item;
                    }
                }
            }

            $data = [
                'ok' => true,
                'results' => $results
            ];
        } catch (Exception $e) {
            logError($e);
            $data = [
                'ok' => false,
                'message' => $e
            ];
        }
        return json_encode($data);
    }

    //Hỗ trợ tải file QR Code
    //CreatedBy nlhoang 21/07/2020
    protected function qrcode()
    {
        try {
            $ids = explode(",", request()->get('ids'));
            $orders = $this->getRepository()->getOrdersByIds($ids);

            $results = [];
            foreach ($orders as $entity) {
                $results[] = [
                    'name' => $entity->order_code,
                    'content' => $this->_generateQRCode($entity)
                ];
            }

            $data = [
                'ok' => true,
                'results' => $results
            ];
        } catch (Exception $e) {
            logError($e);
            $data = [
                'ok' => false,
                'message' => $e->getMessage()
            ];
        }
        return json_encode($data);
    }

    // tính toán tải trọng của chuyến xe
    // created by thaivh 01/08/2020
    protected function _calcCapacity($route, $entity, $vehicle = null)
    {
        $message = [];

        if (empty($route) && empty($vehicle)) {
            return $message;
        }

        $routeOrders = !empty($route) ? $route->orders : [];
        $vehicleRoute = empty($vehicle) ? $route->vehicle : $vehicle;
        $totalWeight = 0;
        $totalVolume = 0;

        if (empty($vehicleRoute)) {
            return $message;
        }

        foreach ($routeOrders as $order) {
            if ($order->id == $entity->id) continue;
            $totalWeight += isset($order->weight) ? $order->weight : 0;
            $totalVolume += isset($order->volume) ? $order->volume : 0;
        }

        if (!empty($vehicleRoute->weight) && ($entity->weight + $totalWeight) > $vehicleRoute->weight) {
            $message[] = trans('validation.out_of_weight', [
                'weight' => numberFormat($entity->weight + $totalWeight),
                'vehicle' => numberFormat($vehicleRoute->weight)
            ]);
        }

        if (!empty($vehicleRoute->volume) && ($entity->volume + $totalVolume) > $vehicleRoute->volume) {
            $message[] = trans('validation.out_of_volume', [
                'volume' => numberFormat($entity->volume + $totalVolume),
                'vehicle' => numberFormat($vehicleRoute->volume)
            ]);
        }

        return $message;
    }


    /**
     * Tính gps_distance của đơn hàng
     * @param $orderEntity
     * @param $locationDestinationId
     * @param $locationArrivalId
     */
    public function calcOrderDistance($orderEntity, $locationDestinationId, $locationArrivalId)
    {
        $distance = 0;
        $googleConstant = new GoogleConstant(env('GOOGLE_MAP_API_KEY', ''));

        $locationDestination = $this->getLocationRepository()->getLocationsById($locationDestinationId);
        $locationArrival = $this->getLocationRepository()->getLocationsById($locationArrivalId);

        if ($locationDestination && $locationArrival) {
            // $distance = $googleConstant->calculateDistance($locationDestination->full_address, $locationArrival->full_address);
            if ($distance <= 0) {
                $latLongDestination = $googleConstant->convertDMSToLatLong($locationDestination->ward_location);
                $latLongArrival = $googleConstant->convertDMSToLatLong($locationArrival->ward_location);

                if (!empty($latLongDestination) && !empty($latLongArrival)) {
                    $pointDestination = [
                        'lat' => $latLongDestination['latitude'],
                        'lng' => $latLongDestination['longitude']
                    ];
                    $pointArrival = [
                        'lat' => $latLongArrival['latitude'],
                        'lng' => $latLongArrival['longitude']
                    ];

                    $distance = $googleConstant->getDistanceBetween($pointDestination, $pointArrival);
                }
            }
        }
        if ($orderEntity && $distance != 0) {
            $orderEntity->gps_distance = $distance;
            $orderEntity->save();
        }
    }

    //Cập nhật doanh thu đơn hàng
    //CreatedBy nlhoang 21/09/2020
    protected function updateRevenue(Request $request)
    {
        try {
            $ids = explode(",", request()->get('ids'));
            $withRelation = [
                'customer',
                'locationDestination',
                'locationArrival'
            ];
            $columns = [
                'orders.*',
                'lpd.title as name_of_province_destination_id',
                'lpa.title as name_of_province_arrival_id',
                'ldd.title as name_of_district_destination_id',
                'lda.title as name_of_district_arrival_id'
            ];
            $items = $this->getRepository()->getItemsByIds($ids, $columns, $withRelation);

            $this->setViewData([
                'items' => $items,
            ]);

            $html = [
                'content' => $this->render('backend.order.update_revenue_modal')->render(),
            ];

            $this->setData($html);
            return $this->renderJson();
        } catch (Exception $e) {
            logError($e . ' - Data ' . json_encode($request));
            $data = [
                'ok' => false,
                'message' => $e->getMessage()
            ];
        }
        return json_encode($data);
    }

    //Cập nhật doanh thu đơn hàng
    //CreatedBy nlhoang 21/09/2020
    protected function massUpdateRevenue(Request $request)
    {
        try {
            $items = Request::get('data');
            foreach ($items as $item) {
                $order = $this->getRepository()->getItemById($item['id']);
                $order->amount = $item['value'];
                $order->save();
            }
            return $this->renderJson();
        } catch (Exception $e) {
            logError($e . ' - Data ' . json_encode($request));
            $data = [
                'ok' => false,
                'message' => $e->getMessage()
            ];
        }
        return json_encode($data);
    }

    //Cập nhật số khung và số model
    //CreatedBy nlhoang 22/09/2020
    protected function updateVinNo(Request $request)
    {
        try {
            $ids = explode(",", request()->get('ids'));
            $withRelation = [
                'customer',
                'locationDestination',
                'locationArrival'.
                'orderCustomer'
            ];
            $items = $this->getRepository()->getItemsByIds($ids, null, $withRelation);

            $this->setViewData([
                'items' => $items,
            ]);

            $html = [
                'content' => $this->render('backend.order.update_vin_no_modal')->render(),
            ];

            $this->setData($html);
            return $this->renderJson();
        } catch (Exception $e) {
            logError($e . ' - Data ' . json_encode($request));
            $data = [
                'ok' => false,
                'message' => $e->getMessage()
            ];
        }
        return json_encode($data);
    }

    //Cập nhật doanh thu đơn hàng
    //CreatedBy nlhoang 22/09/2020
    protected function massUpdateVinNo(Request $request)
    {
        try {
            $items = Request::get('data');
            foreach ($items as $item) {
                $order = $this->getRepository()->getItemById($item['id']);
                $order->vin_no = $item['vin_no'];
                $order->model_no = $item['model_no'];
                $order->save();
            }
            return $this->renderJson();
        } catch (Exception $e) {
            logError($e . ' - Data ' . json_encode($request));
            $data = [
                'ok' => false,
                'message' => $e->getMessage()
            ];
        }
        return json_encode($data);
    }

    // Lấy xe và tài xế form cập nhật đối tác
    public function updatePartnerForm()
    {
        try {
            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));

            $valid = $this->getRepository()->validHasPartnerAccept($orderIds);

            if ($valid) {
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_WARNING,
                    'errorMessage' => 'Tồn tại đơn hàng đối tác vận tải đã xác nhận.',
                ]);
            }

            $result = $this->getRepository()->getDefaultVehicleAndDriverByOrderIDs($orderIds);
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $result
            ]);

        } catch (Exception $e) {
            logError($e);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * TH1: Chỉ cập nhật đối tác
     * TH2: Cập nhật xe và tài xế -> tạo nhiều chuyến
     * TH3 : Cập nhật xe và tài xế -> tạo 1 chuyến
     * @param $orderIds
     * @param $partnerId
     * @param $vehicleId
     * @param $driverId
     * @param $mergeRoute
     * @return string|void
     */
    public function addPartnerToOrder($orderIds, $partnerId, $vehicleId, $driverId, $mergeRoute)
    {
        $message = "";

        if (empty($partnerId)) {
            $message = "Chưa nhập Đối tác vận tải";
            return $message;
        }
        if (!empty($vehicleId) && empty($driverId)) {
            $message = "Chưa nhập Tài xế";
            return $message;
        }
        if (empty($vehicleId) && !empty($driverId)) {
            $message = "Chưa nhập Xe";
            return $message;
        }

        $orderList = $this->getRepository()->getOrdersByIds($orderIds);
        if ($orderList && count($orderList) > 0) {

            $userIds = $this->getAdminUserRepository()->getPartnerUserForNotifyById($partnerId);

            //TH1
            if (empty($vehicleId)) {
                foreach ($orderList as $order) {
                    $order->status_partner = config('constant.PARTNER_CHO_XAC_NHAN');
                    $order->partner_id = $partnerId;
                    $order->save();

                    //Notify cho đối tác

                    $this->getNotificationService()->notifyC20ToPartner(1, $userIds,
                        ['order_code' => $order->order_code, 'order_id' => $order->id]);
                }
            } else {
                //TH2
                foreach ($orderList as $order) {
                    //Cập nhật xe và tài xế vào đơn
                    $this->_processOrderFromRoute(1, $order, $vehicleId, $driverId, null, $partnerId);
                    if (!$mergeRoute) {
                        //Tạo mới chuyến
                        $vehicle = $this->getVehicleRepository()->getItemById($vehicleId);
                        $this->getRouteService()->createNewRoute([$order], $vehicleId, $driverId, $vehicle ? $vehicle->group_id : null);
                    }

                    //Notify cho đối tác
                    $this->getNotificationService()->notifyC20ToPartner(1, $userIds,
                        ['order_code' => $order->order_code, 'order_id' => $order->id]);
                }

                //TH3
                if ($mergeRoute || $mergeRoute == 'true') {
                    //Tạo mới chuyến
                    $vehicle = $this->getVehicleRepository()->getItemById($vehicleId);
                    $this->getRouteService()->createNewRoute($orderList, $vehicleId, $driverId, $vehicle ? $vehicle->group_id : null);

                }
            }
        }

        return $message;
    }

    //API cập nhật đối tác vận tải dh
    function updatePartner(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'order_ids' => 'required',
                'partner_id' => 'required',
            ], [
                'order_ids.required' => 'Bạn chưa chọn đơn hàng',
                'partner_id.required' => 'Đối tác vận tải là bắt buộc'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }

            DB::beginTransaction();

            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));
            $vehicleId = empty(Request::get('vehicle_id')) ? null : Request::get('vehicle_id');
            $driverId = empty(Request::get('driver_id')) ? null : Request::get('driver_id');
            $partnerId = empty(Request::get('partner_id')) ? null : Request::get('partner_id');
            $mergerRoute = empty(Request::get('merge_route')) ? false : Request::get('merge_route');

            $message = $this->addPartnerToOrder($orderIds, $partnerId, $vehicleId, $driverId, $mergerRoute);

            DB::commit();

            return response()->json([
                'errorCode' => empty($message) ? HttpCode::EC_OK : HttpCode::EC_APPLICATION_WARNING,
                'errorMessage' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            logError($e . ' - Data ' . json_encode($request));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    public function showSplitOrder($id)
    {
        if (!request()->ajax()) {
            return $this->_backToIndex();
        }
        $backUrlKey = Request::get('back_url_key', null);
        $grid = Request::get('grid', false);

        $quantities = abs(Request::get('quantities'));

        $entity = $this->getRepository()->findWithRelation($id);

        $entity->orders = $this->getRepository()->getOrdersByIds($id);

        $entity = $this->_processShowListGoods($entity);

        $this->setEntity($entity);

        $routeName = $this->getCurrentRouteName();
        $routePrefix = str_replace('.' . $this->getCurrentAction(false), '', $routeName);
        $url = $grid ? $url = route($routePrefix . '.index') . '#' . $id : '';
        $this->setViewData([
            'quantities' => $quantities,
            'entity' => $entity
        ]);

        $entity = $this->getEntity();
        $html = [
            'content' => $this->render('backend.' . $this->getCurrentControllerName() . '._split_order')->render(),
            'title' => trans('actions.show_detail') . ' <b>' . $entity->getDetailNameField() . '</b>',
            'backUrlKey' => $grid ? Url::generateBackUrlKey($url) : $backUrlKey,
        ];

        $this->setData($html);

        return $this->renderJson();
    }

    public function splitOrderSave(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'order_id' => 'required',
                'order_split_list' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }

            $orderId = Request::get('order_id');
            $dataOrders = Request::get('order_split_list');

            $order = $this->getRepository()->getItemById($orderId);
            $validMessage = $this->getOrderService()->validSplitOrder($order, $dataOrders);
            if (!empty($validMessage)) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validMessage
                ]);
            }

            DB::beginTransaction();

            $this->getOrderService()->splitOrder($order, $dataOrders, config('constant.SOURCE_CREATE_C20_ORDER_SPLIT'));

            DB::commit();

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            logError($exception . '- Data : ' . json_encode($request));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage(),
            ]);
        }
    }

    public function mergeOrderConfirm()
    {
        $errorMessage = '';
        $orderCodes = '';

        $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));
        $orders = $this->getRepository()->getItemsByIds($orderIds);

        if (!$orders || count($orders) < 2) {
            $errorMessage = 'Bạn phải chọn ít nhất 2 đơn đặt hàng';
        } else {
            $validOrderNo = $this->getRepository()->validMatchOrderNo($orderIds);
            if (!$validOrderNo)
                $errorMessage = 'Danh sách Đơn vận tải đã chọn không thuộc cùng Đơn đặt hàng';

            $validOrderStatus = 0;
            foreach ($orders as $order) {
                if ($order->status_partner != config('constant.PARTNER_CHO_GIAO_DOI_TAC_VAN_TAI')) {
                    $validOrderStatus++;
                }
            }
            if ($validOrderStatus > 1)
                $errorMessage = 'Danh sách Đơn vận tải đã chọn tồn tại đơn đã giao cho đối tác vận tải';

            $orderCodes = implode(',', array_filter(array_column($orders->toArray(), 'order_code')));
        }

        $this->setViewData([
            'orderCodes' => $orderCodes,
            'errorMessage' => $errorMessage
        ]);

        return [
            'content' => $this->render('backend.order.merge_order_content')->render(),
        ];

    }

    public function mergeOrderSave(Request $request)
    {
        try {

            $orderIds = empty(Request::get('order_ids')) ? null : explode(',', Request::get('order_ids'));
            DB::beginTransaction();

            $orders = $this->getRepository()->getItemsByIds($orderIds);

            $this->getOrderService()->mergeOrder($orders, config('constant.SOURCE_CREATE_C20_ORDER_MERGE'));

            DB::commit();

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ]);
        } catch (Exception $exception) {
            DB::rollBack();

            logError($exception . '- Data : ' . json_encode($request));
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $exception->getMessage(),
            ]);
        }
    }
}
