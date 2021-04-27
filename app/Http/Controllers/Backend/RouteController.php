<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Exports\RouteExport;
use App\Exports\TemplateExport;
use App\Http\Controllers\Base\BackendController;
use App\Imports\RouteImport;
use App\Model\Entities\ReceiptPayment;
use App\Model\Entities\RouteApprovalHistory;
use App\Model\Entities\RouteCost;
use App\Model\Entities\Routes;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\DriverRepository;
use App\Repositories\FileRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\QuotaCostRepository;
use App\Repositories\QuotaRepository;
use App\Repositories\ReceiptPaymentRepository;
use App\Repositories\RouteApprovalHistoryRepository;
use App\Repositories\RouteCostRepository;
use App\Repositories\RouteFileRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\LocationRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\TemplatePaymentRepository;
use App\Repositories\TemplatePaymentMappingRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\ExcelColumnConfigRepository;
use App\Repositories\SystemConfigRepository;

use App\Helpers\Facades\BatchFacade as Batch;
use App\Services\RouteService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Validator;
use PDF;
use App\Model\Entities\File;

class RouteController extends BackendController
{
    private $_order_ids;
    private $_locations;
    private $_fileIds;
    protected $_locationRepository;
    protected $_orderRepository;
    protected $_vehicleRepository;
    protected $_provinceRepository;
    protected $_driverRepository;
    protected $_routeCostRepository;
    protected $_quotaRepository;
    protected $_quotaCostRepository;
    protected $columnConfigRepository;
    protected $_routeFileRepository;
    protected $_fileRepository;
    protected $_routeApprovalHistoryRepository;
    protected $_receiptPaymentRepository;
    protected $_templateRepository;
    protected $_templatePaymentRepository;
    protected $_templatePaymentMappingRepository;
    protected $_excelColumnConfigRepository;
    protected $_systemConfigRepository;
    protected $_routeService;


    /**
     * @return mixed
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
     * @return OrderRepository
     */
    public function getOrderRepository()
    {
        return $this->_orderRepository;
    }

    /**
     * @param $orderRepository
     */
    public function setOrderRepository($orderRepository): void
    {
        $this->_orderRepository = $orderRepository;
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
     * @return VehicleRepository
     */
    public function getVehicleRepository()
    {
        return $this->_vehicleRepository;
    }

    /**
     * @param $vehicleRepository
     */
    public function setVehicleRepository($vehicleRepository): void
    {
        $this->_vehicleRepository = $vehicleRepository;
    }

    /**
     * @return DriverRepository
     */
    public function getDriverRepository()
    {
        return $this->_driverRepository;
    }

    /**
     * @param $driverRepository
     */
    public function setDriverRepository($driverRepository): void
    {
        $this->_driverRepository = $driverRepository;
    }

    /**
     * @return RouteCostRepository
     */
    public function getRouteCostRepository()
    {
        return $this->_routeCostRepository;
    }

    /**
     * @param $routeCostRepository
     */
    public function setRouteCostRepository($routeCostRepository): void
    {
        $this->_routeCostRepository = $routeCostRepository;
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
     * @return QuotaCostRepository
     */
    public function getQuotaCostRepository()
    {
        return $this->_quotaCostRepository;
    }

    /**
     * @param $quotaCostRepository
     */
    public function setQuotaCostRepository($quotaCostRepository): void
    {
        $this->_quotaCostRepository = $quotaCostRepository;
    }

    /**
     * @return ColumnConfigRepository
     */
    public function getColumnConfigRepository()
    {
        return $this->columnConfigRepository;
    }

    /**
     * @param $columnConfigRepository
     */
    public function setColumnConfigRepository($columnConfigRepository): void
    {
        $this->columnConfigRepository = $columnConfigRepository;
    }

    /**
     * @return RouteFileRepository
     */
    public function getRouteFileRepository()
    {
        return $this->_routeFileRepository;
    }

    /**
     * @param $routeFileRepository
     */
    public function setRouteFileRepository($routeFileRepository): void
    {
        $this->_routeFileRepository = $routeFileRepository;
    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function getRouteApprovalHistoryRepository()
    {
        return $this->_routeApprovalHistoryRepository;
    }

    /**
     * @param mixed _routeApprovalHistoryRepository
     */
    public function setRouteApprovalHistoryRepository($routeApprovalHistoryRepository): void
    {
        $this->_routeApprovalHistoryRepository = $routeApprovalHistoryRepository;
    }

    /**
     * @return ReceiptPaymentRepository
     */
    public function getReceiptPaymentRepository()
    {
        return $this->_receiptPaymentRepository;
    }

    /**
     * @param null $receiptPaymentRepository
     */
    public function setReceiptPaymentRepository($receiptPaymentRepository): void
    {
        $this->_receiptPaymentRepository = $receiptPaymentRepository;
    }

    /**
     * @param TemplateRepository $templateRepository
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
     * @param $templatePaymentRepository
     */
    public function setTemplatePaymentRepository($templatePaymentRepository)
    {
        $this->_templatePaymentRepository = $templatePaymentRepository;
    }

    /**
     * @return TemplatePaymentRepository
     */
    public function getTemplatePaymentRepository()
    {
        return $this->_templatePaymentRepository;
    }

    /**
     * @param $templatePaymentMappingRepository
     */
    public function setTemplatePaymentMappingRepository($templatePaymentMappingRepository)
    {
        $this->_templatePaymentMappingRepository = $templatePaymentMappingRepository;
    }

    /**
     * @return TemplatePaymentMappingRepository
     */
    public function getTemplatePaymentMappingRepository()
    {
        return $this->_templatePaymentMappingRepository;
    }

    /**
     * @return mixed
     */
    public function getExcelColumnConfigRepository()
    {
        return $this->_excelColumnConfigRepository;
    }

    /**
     * @param mixed $orderPaymentRepos
     */
    public function setExcelColumnConfigRepository($excelColumnConfigRepository): void
    {
        $this->_excelColumnConfigRepository = $excelColumnConfigRepository;
    }

    /**
     * @return mixed
     */
    public function getSystemConfigRepository()
    {
        return $this->_systemConfigRepository;
    }

    /**
     * @param mixed $orderPaymentRepos
     */
    public function setSystemConfigRepository($systemConfigRepository): void
    {
        $this->_systemConfigRepository = $systemConfigRepository;
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

    public function __construct(
        RoutesRepository $routeRepository,
        LocationRepository $locationRepository,
        OrderRepository $orderRepository,
        RouteCostRepository $routeCostRepository,
        VehicleRepository $vehicleRepository,
        ProvinceRepository $provinceRepository,
        DriverRepository $driverRepository,
        QuotaRepository $quotaRepository,
        QuotaCostRepository $quotaCostRepository,
        ColumnConfigRepository $columnConfigRepository,
        RouteFileRepository $routeFileRepository,
        FileRepository $fileRepository,
        RouteApprovalHistoryRepository $routeApprovalHistoryRepository,
        ReceiptPaymentRepository $receiptPaymentRepository,
        TemplateRepository $templateRepository,
        TemplatePaymentRepository $templatePaymentRepository,
        TemplatePaymentMappingRepository $templatePaymentMappingRepository,
        ExcelColumnConfigRepository $excelColumnConfigRepository,
        SystemConfigRepository $systemConfigRepository,
        RouteService $routeService

    )
    {
        parent::__construct();
        $this->setRepository($routeRepository);
        $this->setBackUrlDefault('route.index');
        $this->setConfirmRoute('route.confirm');
        $this->setMenu('order');
        $this->setTitle(trans('models.route.name'));

        $this->setLocationRepository($locationRepository);
        $this->setOrderRepository($orderRepository);
        $this->setRouteCostRepository($routeCostRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setProvinceRepository($provinceRepository);
        $this->setDriverRepository($driverRepository);
        $this->setQuotaRepository($quotaRepository);
        $this->setQuotaCostRepository($quotaCostRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setRouteFileRepository($routeFileRepository);
        $this->setFileRepository($fileRepository);
        $this->setRouteApprovalHistoryRepository($routeApprovalHistoryRepository);
        $this->setReceiptPaymentRepository($receiptPaymentRepository);
        $this->setTemplateRepository($templateRepository);
        $this->setTemplatePaymentRepository($templatePaymentRepository);
        $this->setTemplatePaymentMappingRepository($templatePaymentMappingRepository);
        $this->setExcelColumnConfigRepository($excelColumnConfigRepository);
        $this->setSystemConfigRepository($systemConfigRepository);
        $this->setRouteService($routeService);


        $this->setMap(true);
        $this->setAuditing(true);
        $this->setExcel(false);
        $this->setExcelUpdate(false);
    }

    /**
     * @return RedirectResponse
     */
    public function store()
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.create_failed'));
        }
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForStore();
            $this->fireEvent('before_store', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $this->beforeSave($entity);
            $entity->save();
            $this->_saveRelations($entity);
            // add new
            $this->fireEvent('after_store', $entity);
            DB::commit();
            return $this->_backToStart()->with('success', trans('messages.create_success'));
        } catch (Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.create_failed'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function update($id)
    {
        if ($this->_emptyFormData()) {
            return $this->_backToStart()->withErrors(trans('messages.update_failed'));
        }
        $isValid = $this->getRepository()->getValidator()->validateShow($id);
        if (!$isValid) {
            return $this->_backToStart()->withErrors($this->getRepository()->getValidator()->errors());
        }
        DB::beginTransaction();
        try {
            $entity = $this->_findEntityForUpdate($id);
            $this->fireEvent('before_update', $entity);
            $this->_moveFileFromTmpToMedia($entity);
            $this->beforeSave($entity);

            $entity->save();
            $this->_saveRelations($entity, 'edit');
            DB::commit();
            $this->fireEvent('after_update', $entity);
            return $this->_backToStart()->with('success', trans('messages.update_success'));
        } catch (Exception $e) {
            logError($e);
            $this->_removeMediaFile(isset($entity) ? $entity : null);
            DB::rollBack();
        }
        return $this->_backToStart()->withErrors(trans('messages.update_failed'));
    }

    public function _deleteRelations($entity)
    {
        $orders = $this->getOrderRepository()->getOrdersByRouteId($entity->id);
        foreach ($orders as $order) {
            //Xử lý đơn
            app('App\Http\Controllers\Backend\OrderController')->_processOrderFromRouteDelete($order);
        }
        //Xóa cost của route
        $this->getRouteCostRepository()->deleteWhere([
            'route_id' => $entity->id
        ]);
    }

    protected function _saveRelations($entity, $action = 'save')
    {
        //   parent::_saveRelations($entity, $action);
        $this->_processCreateRelation($entity, $action, $this->_order_ids, $this->_locations, $this->_fileIds);
    }

    public function _processCreateRelation($entity, $action, $orderIds, $locations, $files)
    {

        $ETD_date = null;
        $ETD_time = null;
        $ETA_date = null;
        $ETA_time = null;
        $ETD_date_reality = null;
        $ETD_time_reality = null;
        $ETA_date_reality = null;
        $ETA_time_reality = null;
        $location_destination_id = null;
        $location_arrival_id = null;
        $totalWeight = 0;
        $totalVolume = 0;
        $status = config('constant.status_incomplete');

        $orders = $this->getOrderRepository()->getOrdersByIds($orderIds);

        $countCancel = 0;
        $countComplete = 0;
        foreach ($orders as $order) {
            //Xư lý đơn hàng
            app('App\Http\Controllers\Backend\OrderController')->_processOrderFromRoute(
                $action == 'save' ? 1 : 2,
                $order,
                $entity->vehicle_id,
                $entity->driver_id,
                $entity->id
            );

            if ($order->status == config('constant.HUY'))
                $countCancel++;
            if ($order->status == config('constant.HOAN_THANH'))
                $countComplete++;

            if ($order->ETD_date != null) {
                $dateTime1 = $order->ETD_date . ' ' . ($order->ETD_time ? $order->ETD_time : '');
                $dateTime2 = $ETD_date . ' ' . ($ETD_time ? $ETD_time : '');
                if ($ETD_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                    $ETD_date = $order->ETD_date;
                    $ETD_time = $order->ETD_time;
                    $location_destination_id = $order->location_destination_id;
                }
            }
            if ($order->ETA_date != null) {
                $dateTime2 = $order->ETA_date . ' ' . ($order->ETA_time ? $order->ETA_time : '');
                $dateTime1 = $ETA_date . ' ' . ($ETA_time ? $ETA_time : '');
                if ($ETA_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                    $ETA_date = $order->ETA_date;
                    $ETA_time = $order->ETA_time;
                    $location_arrival_id = $order->location_arrival_id;
                }
            }

            if ($order->ETA_date_reality != null) {
                $dateTime2 = $order->ETA_date_reality . ' ' . ($order->ETA_time_reality ? $order->ETA_time_reality : '');
                $dateTime1 = $ETA_date_reality . ' ' . ($ETA_time_reality ? $ETA_time_reality : '');
                if ($ETA_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                    $ETA_date_reality = $order->ETA_date_reality;
                    $ETA_time_reality = $order->ETA_time_reality;
                }
            }

            if ($order->ETD_date_reality != null) {
                $dateTime1 = $order->ETD_date_reality . ' ' . ($order->ETD_time_reality ? $order->ETD_time_reality : '');
                $dateTime2 = $ETD_date_reality . ' ' . ($ETD_time_reality ? $ETD_time_reality : '');
                if ($ETD_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                    $ETD_date_reality = $order->ETD_date_reality;
                    $ETD_time_reality = $order->ETD_time_reality;
                }
            }

            $totalWeight += isset($order->weight) && is_numeric($order->weight) ? $order->weight : 0;
            $totalVolume += isset($order->volume) && is_numeric($order->volume) ? $order->volume : 0;
        }
        if ($orders != null && count($orders) > 0) {
            if ($countCancel == count($orders))
                $status = config('constant.status_cancel');
            else if (
                $countComplete == count($orders) ||
                ($countComplete > 0 && ($countComplete + $countCancel) == count($orders))
            )
                $status = config('constant.status_complete');
        } else {
            $status = config('constant.status_complete');
        }

        $vehicle = $this->getVehicleRepository()->search(['id_eq' => $entity->vehicle_id])->first();
        if (!empty($vehicle)) {
            $entity->capacity_weight_ratio = empty($vehicle->weight) || $vehicle->weight == 0 ? 100 : round(($totalWeight / $vehicle->weight) * 100, 2);
            $entity->capacity_volume_ratio = empty($vehicle->volume) || $vehicle->volume == 0 ? 100 : round(($totalVolume / $vehicle->volume) * 100, 2);
        }

        $entity->ETD_date = $ETD_date;
        $entity->ETD_time = $ETD_time;
        $entity->ETA_date = $ETA_date;
        $entity->ETA_time = $ETA_time;
        $entity->ETD_date_reality = $ETD_date_reality;
        $entity->ETD_time_reality = $ETD_time_reality;
        $entity->ETA_date_reality = $ETA_date_reality;
        $entity->ETA_time_reality = $ETA_time_reality;
        $entity->location_destination_id = $location_destination_id;
        $entity->location_arrival_id = $location_arrival_id;
        $entity->route_status = $status;

        //Lưu thông tin dư thừa trên chuyến
        $entity = $this->getRouteService()->saveRouteExtend($entity, $orders);

        $entity->save();

        //Cập nhật route cost
        $this->getRouteService()->_updateRouteCost($entity, $entity->listCost);

        //Lưu file của chuyến
        if (!empty($files)) {
            $this->getRouteFileRepository()->deleteWhere([
                'route_id' => $entity->id
            ]);
            $file_id_list = explode(';', $files);
            if (!empty($file_id_list)) {
                foreach ($file_id_list as $file_id) {
                    $routeFile = $this->getRouteFileRepository()->findFirstOrNew([]);
                    $routeFile->route_id = $entity->id;
                    $routeFile->file_id = $file_id;
                    $routeFile->save();
                }
            }
        }
    }

    public function _prepareForm()
    {
        $this->setViewData([
            'provinceList' => $this->getProvinceRepository()->getListForSelect(),
            'districtList' => [],
            'wardList' => [],
        ]);
    }

    protected function _prepareAfterSetEntity($prepare)
    {
        if ($prepare instanceof RedirectResponse) {
            return;
        }

        $entity = $this->getEntity();
        $entity = $this->_processDate(AppConstant::DATE_YMD, AppConstant::DATE_DMY, $entity);

        $vehicleEntity = [
            'id' => $entity->vehicle ? $entity->vehicle->id : null,
            'reg_no' => $entity->vehicle ? $entity->vehicle->reg_no : null
        ];
        $primaryDriverEntity = [
            'id' => $entity->driver ? $entity->driver->id : null,
            'name' => $entity->driver ? $entity->driver->full_name : null
        ];
        $quotaEntity = [
            'id' => $entity->quota ? $entity->quota->id : null,
            'name' => $entity->quota ? $entity->quota->name : null
        ];
        $locationDestination = [
            'id' => $entity->locationDestination ? $entity->locationDestination->id : null,
            'title' => $entity->locationDestination ? $entity->locationDestination->title : null
        ];
        $locationArrival = [
            'id' => $entity->locationArrival ? $entity->locationArrival->id : null,
            'title' => $entity->locationArrival ? $entity->locationArrival->title : null
        ];

        $this->setViewData([
            'vehicleEntity' => $vehicleEntity,
            'primaryDriverEntity' => $primaryDriverEntity,
            'quotaEntity' => $quotaEntity,
            'receiptPayments' => ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true),
            'locationDestination' => $locationDestination,
            'locationArrival' => $locationArrival
        ]);
    }

    protected function _prepareCreate()
    {
        $vehicleEntity = [
            'id' => '',
            'reg_no' => ''
        ];
        $primaryDriverEntity = [
            'id' => '',
            'name' => ''
        ];
        $quotaEntity = [
            'id' => '',
            'name' => ''
        ];
        $this->setViewData([
            'vehicleEntity' => $vehicleEntity,
            'primaryDriverEntity' => $primaryDriverEntity,
            'quotaEntity' => $quotaEntity,
            'receiptPayments' => ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true),
        ]);
        return parent::_prepareCreate();
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('route_code', $attributes)) {
            $code = $attributes['route_code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_route'));
            }
        }

        //Lấy danh sách order của route
        $orders = null;
        $ETD_date = null;
        $ETD_time = null;
        $ETA_date = null;
        $ETA_time = null;
        $ETD_date_reality = null;
        $ETD_time_reality = null;
        $locationDestination = null;
        $locationArrival = null;
        if (array_key_exists('order_ids', $attributes)) {
            $orders = $this->getOrderRepository()->getOrdersByIds($attributes['order_ids']);
            foreach ($orders as $order) {
                if ($order->ETD_date != null) {
                    $dateTime1 = $order->ETD_date . ' ' . ($order->ETD_time ? $order->ETD_time : '');
                    $dateTime2 = $ETD_date . ' ' . ($ETD_time ? $ETD_time : '');
                    if ($ETD_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                        $ETD_date = $order->ETD_date;
                        $ETD_time = $order->ETD_time;
                        $locationDestination = [
                            'id' => $order->location_destination_id,
                            'title' => $order->location_destination_title
                        ];
                    }
                }
                if ($order->ETA_date != null) {
                    $dateTime2 = $order->ETA_date . ' ' . ($order->ETA_time ? $order->ETA_time : '');
                    $dateTime1 = $ETA_date . ' ' . ($ETA_time ? $ETA_time : '');
                    if ($ETA_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                        $ETA_date = $order->ETA_date;
                        $ETA_time = $order->ETA_time;
                        $locationArrival = [
                            'id' => $order->location_arrival_id,
                            'title' => $order->location_arrival_title
                        ];
                    }
                }
            }

            $orders = $orders->pluck('order_code', 'id');
        } else if ($id != -1) {
            $orders = $this->getOrderRepository()->getOrdersByRouteId($id)->pluck('order_code', 'id');
        }

        //Lấy danh sách location của route
        $locations = null;
        if (array_key_exists('locations', $attributes)) {
            $locations = $attributes['locations'];
        } else if ($id != -1) {

            $locations = $this->getOrderRepository()->getRouteLocations($id);
            if ($locations) {
                foreach ($locations as &$location) {
                    $location->destination_location_title = $location->locationDestination ? $location->locationDestination->title : $location->destination_location_title;
                    $location->arrival_location_title = $location->locationArrival ? $location->locationArrival->title : $location->arrival_location_title;
                }
                $locations = json_encode($locations);
            }
        }

        //Lấy danh sách cost của route với quota_id
        $costs = null;
        $costsJson = "";
        if (array_key_exists('costs', $attributes)) { // Khi back lại từ confirm
            $costsJson = $attributes['costs'];
            $costs = json_decode($attributes['costs'], true);
        } else if ($id != -1) { //Sửa chuyến xe

            $costs = $this->getRouteCostRepository()->getCosts($id);
            if ($costs) {
                foreach ($costs as $i => &$cost) {
                    if ((empty($cost->amount_admin) || $cost->amount_admin == 0)
                        && (empty($cost->amount_driver) || $cost->amount_driver == 0)
                        && (empty($cost->amount) || $cost->amount == 0)
                    ) {
                        unset($costs[$i]);
                        continue;
                    }
                    $cost->receipt_payment_name = $cost->receiptPayment ? $cost->receiptPayment->name : $cost->receipt_payment_name;
                }
                $costsJson = json_encode($costs);
                $costs = $costs->toArray();
            }
        }

        $total_cost = 0;
        if (isset($costs) && is_array($costs) && 0 < count($costs)) {
            foreach ($costs as $cost) {
                $total_cost += isset($cost['amount_admin']) ? $cost['amount_admin'] : 0;
            }
        }

        //Lấy danh sách file
        $str_files = null;
        if (array_key_exists('file_id', $attributes)) {
            $str_files = $attributes['file_id'];
        } else {
            $routeFileIds = $this->getRouteFileRepository()->getRouteFileIdWithRouteID($id, config('constant.ROUTE_FILE_TYPE_GENERAL', '0'));
            if ($routeFileIds != null)
                $str_files = implode(';', $routeFileIds);
        }
        $this->setFileListForFormData($str_files);

        $this->setViewData([
            'code' => $code,
            'locations' => $locations,
            'costs' => $costs,
            'costsJson' => $costsJson,
            'orders' => $orders,
            'total_cost' => $total_cost,
            'ETD_time' => $ETD_time,
            'ETD_date' => $ETD_date,
            'ETA_time' => $ETA_time,
            'ETA_date' => $ETA_date,
            'locationDestination' => $locationDestination,
            'locationArrival' => $locationArrival
        ]);
    }

    public function setFileListForFormData($str_files)
    {
        $file_list = [];
        if (!empty($str_files)) {
            $file_id_list = explode(';', $str_files);
            if (!empty($file_id_list)) {
                foreach ($file_id_list as $file_id) {
                    $file = $this->getFileRepository()->getFileWithID($file_id);
                    if ($file != null)
                        $file_list[] = $file;
                }
            }
        }
        $this->setViewData([
            'file_list' => $file_list,
        ]);
    }

    protected function _prepareConfirm()
    {
        $entity = $this->_getFormData();

        //Lấy danh sách order của route
        $orders = $this->getOrderRepository()->getOrdersByIds($entity->order_ids);

        $ETD_date = null;
        $ETD_time = null;
        $ETA_date = null;
        $ETA_time = null;
        $ETD_date_reality = null;
        $ETD_time_reality = null;
        $ETA_date_reality = null;
        $ETA_time_reality = null;
        $location_destination_title = null;
        $location_arrival_title = null;

        $totalWeight = 0;
        $totalVolume = 0;

        $status = config('constant.status_incomplete');
        $countCancel = 0;
        $countComplete = 0;
        $vehicle = $this->getVehicleRepository()->search(['id_eq' => $entity->vehicle_id])->first();

        foreach ($orders as $order) {
            if ($order->status == config('constant.HUY'))
                $countCancel++;

            if ($order->status == config('constant.HOAN_THANH'))
                $countComplete++;

            if ($order->ETD_date != null) {
                $dateTime1 = $order->ETD_date . ' ' . ($order->ETD_time ? $order->ETD_time : '');
                $dateTime2 = $ETD_date . ' ' . ($ETD_time ? $ETD_time : '');
                if ($ETD_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                    $ETD_date = $order->ETD_date;
                    $ETD_time = $order->ETD_time;
                    $location_destination_title = $order->location_destination_title;
                }
            }
            if ($order->ETA_date != null) {
                $dateTime2 = $order->ETA_date . ' ' . ($order->ETA_time ? $order->ETA_time : '');
                $dateTime1 = $ETA_date . ' ' . ($ETA_time ? $ETA_time : '');
                if ($ETA_date == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                    $ETA_date = $order->ETA_date;
                    $ETA_time = $order->ETA_time;
                    $location_arrival_title = $order->location_arrival_title;
                }
            }

            if ($order->ETA_date_reality != null) {
                $dateTime2 = $order->ETA_date_reality . ' ' . ($order->ETA_time_reality ? $order->ETA_time_reality : '');
                $dateTime1 = $ETA_date_reality . ' ' . ($ETA_time_reality ? $ETA_time_reality : '');
                if ($ETA_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                    $ETA_date_reality = $order->ETA_date_reality;
                    $ETA_time_reality = $order->ETA_time_reality;
                }
            }

            if ($order->ETD_date_reality != null) {
                $dateTime1 = $order->ETD_date_reality . ' ' . ($order->ETD_time_reality ? $order->ETD_time_reality : '');
                $dateTime2 = $ETD_date_reality . ' ' . ($ETD_time_reality ? $ETD_time_reality : '');
                if ($ETD_date_reality == null || AppConstant::isDate2GreatDate1($dateTime1, $dateTime2)) {
                    $ETD_date_reality = $order->ETD_date_reality;
                    $ETD_time_reality = $order->ETD_time_reality;
                }
            }
            $totalWeight += isset($order->weight) && is_numeric($order->weight) ? $order->weight : 0;
            $totalVolume += isset($order->volume) && is_numeric($order->volume) ? $order->volume : 0;
        }

        if ($orders != null && count($orders) > 0) {
            if ($countCancel == count($orders))
                $status = config('constant.status_cancel');
            else if (
                $countComplete == count($orders) ||
                ($countComplete > 0 && ($countComplete + $countCancel) == count($orders))
            )
                $status = config('constant.status_complete');
        } else {
            $status = config('constant.status_complete');
        }

        $entity->ETD_date = $ETD_date;
        $entity->ETD_time = $ETD_time;
        $entity->ETA_date = $ETA_date;
        $entity->ETA_time = $ETA_time;
        $entity->ETD_date_reality = $ETD_date_reality;
        $entity->ETD_time_reality = $ETD_time_reality;
        $entity->ETA_date_reality = $ETA_date_reality;
        $entity->ETA_time_reality = $ETA_time_reality;
        $entity->location_destination_title = $location_destination_title;
        $entity->location_arrival_title = $location_arrival_title;
        $entity->route_status = $status;

        //Lấy danh sách location của route
        $locations = json_decode($entity->locations, true);

        $entity->orders = $orders;
        $entity->locations = $locations;

        if (!empty($vehicle)) {
            $entity->capacity_weight_ratio = empty($vehicle->weight) || $vehicle->weight == 0 ? 100 : round(($totalWeight / $vehicle->weight) * 100, 2);
            $entity->capacity_volume_ratio = empty($vehicle->volume) || $vehicle->volume == 0 ? 100 : round(($totalVolume / $vehicle->volume) * 100, 2);
        }

        $this->setEntity($entity);

        //Lấy danh sách file
        $attributes = $this->_getFormData()->getAttributes();
        $this->setFileListForFormData($attributes['file_id']);

        $this->setViewData([
            'show_history' => false,
        ]);
    }

    protected function _prepareShow($id)
    {
        $entity = $this->getRepository()->findWithRelation($id);

        //Lấy danh sách location của route
        $locations = $this->getOrderRepository()->getRouteLocations($id);
        foreach ($locations as &$location) {
            $location->destination_location_title = $location->locationDestination ? $location->locationDestination->title : $location->destination_location_title;
            $location->arrival_location_title = $location->locationArrival ? $location->locationArrival->title : $location->arrival_location_title;

            // Vi tri diem dau diem cuoi chuyen
            if ($entity->location_destination_id && $entity->location_destination_id == $location->destination_location_id) {
                $entity->location_destination_title = $location->destination_location_title;
            }
            if ($entity->location_arrival_id && $entity->location_arrival_id == $location->arrival_location_id) {
                $entity->location_arrival_title = $location->arrival_location_title;
            }
        }
        if ($locations)
            $locations = $locations->toArray();

        //Lấy danh sách cost của route
        $costs = $this->getRouteCostRepository()->getCosts($id);
        $totalCostAdmin = 0;
        $totalCostDriver = 0;
        foreach ($costs as $i => &$cost) {
            if ((empty($cost->amount_admin) || $cost->amount_admin == 0)
                && (empty($cost->amount_driver) || $cost->amount_driver == 0)
                && (empty($cost->amount) || $cost->amount == 0)
            ) {
                unset($costs[$i]);
                continue;
            }
            $cost->receipt_payment_name = $cost->receiptPayment ? $cost->receiptPayment->name : $cost->receipt_payment_name;
            $totalCostAdmin += $cost->amount_admin ? $cost->amount_admin : 0;
            $totalCostDriver += $cost->amount_driver ? $cost->amount_driver : 0;
        }

        $entity->orders = $this->getRepository()->getOrdersByID($id);
        $entity->locations = $locations;
        $entity->costs = $costs;
        $entity->total_cost_admin = $totalCostAdmin;
        $entity->total_cost_driver = $totalCostDriver;

        $entity = $this->_processDate(AppConstant::DATE_YMD, AppConstant::DATE_DMY, $entity);
        $entity->listCost = $entity->costs->toArray();

        $this->setEntity($entity);

        //Lấy danh sách file
        $str_files = null;
        $routeFileIds = $this->getRouteFileRepository()->getRouteFileIdWithRouteID($id, config('constant.ROUTE_FILE_TYPE_GENERAL', '0'));
        if ($routeFileIds != null)
            $str_files = implode(';', $routeFileIds);
        $this->setFileListForFormData($str_files);

        $fileCostList = null;
        $routeFileCosts = $this->getRouteFileRepository()->getRouteFileCostWithRouteID($id);
        if ($routeFileCosts != null) {
            foreach ($routeFileCosts as $routeFile) {
                if (isset($fileCostList[$routeFile->cost_id])) {
                    $fileCostList[$routeFile->cost_id][] = $routeFile->file_id;
                } else {
                    unset($files);
                    $files[] = $routeFile->file_id;
                    $fileCostList[$routeFile->cost_id] = $files;
                }
            }
        }

        $isModal = Request::get('is_modal', 'f');
        $approved_histories = RouteApprovalHistory::with('approvedUser')->where('route_id', '=', $id)->get();;
        $this->setViewData([
            'show_history' => true,
            'fileCostList' => $fileCostList,
            'isModal' => $isModal,
            'approved_histories' => $approved_histories
        ]);
    }

    public function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_route'));
        $dayCondition = env('DAY_CONDITION_DEFAULT', 4);

        $this->setViewData([
            'dayCondition' => $dayCondition,
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }

    protected function beforeSave($entity)
    {
        $this->_order_ids = $entity->order_ids;
        $this->_locations = json_decode($entity->locations, true);
        $this->_fileIds = $entity->file_id;
    }

    protected function _findEntityForUpdate($id)
    {
        $entity = $this->_findOrNewEntity(null, false, true);
        return $this->_processDate(AppConstant::DATE_DMY, AppConstant::DATE_YMD, $entity);
    }

    protected function _findEntityForStore()
    {
        $entity = $this->_findOrNewEntity(null, false);
        return $this->_processDate(AppConstant::DATE_DMY, AppConstant::DATE_YMD, $entity);
    }

    public function _processDate($patternFrom, $patternTo, $entity)
    {
        empty($entity->ETD_date) || !AppConstant::isDatePattern($patternFrom, $entity->ETD_date) ? $entity->ETD_date = null : $entity->ETD_date = Carbon::createFromFormat($patternFrom, $entity->ETD_date)->format($patternTo);
        empty($entity->ETA_date) || !AppConstant::isDatePattern($patternFrom, $entity->ETA_date) ? $entity->ETA_date = null : $entity->ETA_date = Carbon::createFromFormat($patternFrom, $entity->ETA_date)->format($patternTo);
        $entity->ETD_time = empty($entity->ETD_time) ? null : $entity->ETD_time;
        $entity->ETA_time = empty($entity->ETA_time) ? null : $entity->ETA_time;
        return $entity;
    }

    // Lấy chuyến xe cho select2
    public function getDataForComboBox()
    {
        $all = Request::get('all');
        $q = Request::get('q');
        $currentUser = $this->getCurrentUser();
        $vehicleID = Request::get('vehicle_id');
        $driverID = Request::get('driver_id');
        $params = [
            'driverID' => $driverID,
            'vehicleID' => $vehicleID
        ];

        $partnerId = empty(Request::get('partner_id')) ? $currentUser->partner_id : Request::get('partner_id');

        $data = $this->getRepository()->getItemsByUserID($all, $q, $currentUser->id, $partnerId, $params);
        return response()->json($data);
    }

    //API lấy thông tin đơn hàng khi thêm đơn hàng từ combobox đơn
    function getLocationsByOrder()
    {
        $data = [];
        try {
            $orderId = Request::get('order_id', null);
            $order = $this->getOrderRepository()->getOrdersByIds([$orderId])->first();

            $location = [];
            if ($order != null) {
                $location = [
                    'destination_location_id' => $order->location_destination_id,
                    'destination_location_title' => $order->location_destination_title,
                    'destination_location_date' => $order->ETD_date,
                    'destination_location_time' => $order->ETD_time,
                    'arrival_location_id' => $order->location_arrival_id,
                    'arrival_location_title' => $order->location_arrival_title,
                    'arrival_location_date' => $order->ETA_date,
                    'arrival_location_time' => $order->ETA_time,
                    'order_id' => $order->id,
                    'order_code' => $order->order_code,
                    'order_cost' => $order->extend_cost ? $order->extend_cost : 0
                ];
            }

            $vehicleAndDriver = $this->getOrderRepository()->getVehicleAndDriverForOrder($orderId);
            $data = [
                'location' => $location,
                'vehicleAndDriver' => $vehicleAndDriver
            ];

            return response()->json($data);
        } catch (Exception $e) {
            logError($e);
            return response()->json($data);
        }
    }

    protected function _prepareEdit($id = null)
    {
        $parent = parent::_prepareEdit($id);
        /** @var Routes $entity */
        $entity = $this->getEntity();
        $entity->listCost = empty($entity->listCost) ? $entity->costs->toArray() : $entity->listCost;

        $this->setEntity($entity);
        return $parent;
    }

    public function approval($id)
    {
        /** @var Routes $entity */
        $entity = $this->getRepository()->find($id);
        if (empty($entity)) {
            return $this->_backToIndex();
        }

        if (request()->isMethod('get')) {
            foreach ($entity->costs as $i => &$cost) {
                if ((empty($cost->amount_admin) || $cost->amount_admin == 0)
                    && (empty($cost->amount_driver) || $cost->amount_driver == 0)
                    && (empty($cost->amount) || $cost->amount == 0)
                ) {
                    unset($entity->costs[$i]);
                    $cost->delete();
                    continue;
                }
                $cost->receipt_payment_name = $cost->receiptPayment ? $cost->receiptPayment->name : $cost->receipt_payment_name;
            }
            $listCost = $entity->costs->toArray();
            $fuelCost = $this->getRepository()->getFuelCostHint($id);
            $this->setViewData([]);
            $this->setViewData([
                'listCost' => $listCost,
                'entity' => $entity,
                'fuelCost' => $fuelCost,
                'receiptPayments' => ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true),
            ]);

            $html = [
                'content' => $this->render('backend.route._approval')->render(),
            ];

            $this->setData($html);
            return $this->renderJson();
        }

        if (request()->isMethod('post')) {
            try {
                $listCost = Request::get('listCost');
                $approvedNote = Request::get('note');
                $total = 0;
                foreach ($listCost as $cost) {
                    if ($cost['isInserted'] == 'true') {
                        //Cost dc thêm mới trên admin
                        $routeCostItem = $this->getRouteCostRepository()->findFirstOrNew([]);
                        $routeCostItem->route_id = $id;
                        $routeCostItem->receipt_payment_id = $cost['id'];
                        $routeCostItem->receipt_payment_name = $cost['name'];
                        $routeCostItem->amount_driver = convertNumber($cost['value']);
                        $routeCostItem->amount = convertNumber($cost['value']);
                        $total += $routeCostItem->amount;
                        $routeCostItem->save();
                    } else {
                        $routeCostItem = $entity->costs()->where('id', '=', $cost['id'])->first();
                        $routeCostItem->amount = convertNumber($cost['value']);
                        $total += $routeCostItem->amount;
                        $routeCostItem->save();
                    }
                }

                $entity->final_cost = $total;
                $entity->is_approved = 1;
                $entity->approved_id = Auth::User()->id;
                $entity->approved_date = now();
                $entity->approved_note = $approvedNote;
                $entity->save();

                //Lưu history
                $routeApprovalHistory = $this->getRouteApprovalHistoryRepository()->findFirstOrNew([]);
                $routeApprovalHistory->route_id = $entity->id;
                $routeApprovalHistory->approved_id = Auth::User()->id;
                $routeApprovalHistory->approved_date = now();
                $routeApprovalHistory->approved_note = $approvedNote;
                $routeApprovalHistory->save();

                $totalCostAdmin = 0;
                $totalCostDriver = 0;
                foreach ($entity->costs as &$cost) {
                    $cost->receipt_payment_name = $cost->receiptPayment ? $cost->receiptPayment->name : $cost->receipt_payment_name;
                    $totalCostAdmin += $cost->amount_admin ? $cost->amount_admin : 0;
                    $totalCostDriver += $cost->amount_driver ? $cost->amount_driver : 0;
                }
                $entity->total_cost_admin = $totalCostAdmin;
                $entity->total_cost_driver = $totalCostDriver;
                $entity->listCost = $entity->costs->toArray();
                $this->setViewData([
                    'entity' => $entity
                ]);

                $html = [
                    'content' => $this->render('backend.route._list_cost')->render(),
                ];

                $this->setData($html);
            } catch (Exception $e) {
                logError($e);
            }
        }

        return $this->renderJson();
    }

    protected function _processQuickSave($id, $field, $value)
    {
        $entity = $this->getRepository()->getItemById($id);
        if ($entity != null) {
            $entity->$field = $value;
            $entity->save();
        }
    }

    public function export()
    {
        ini_set('max_execution_time', 8000000);
        $ids = Request::get('ids', null);
        $data = $this->_getDataIndex(false);

        if (isset($ids)) {
            $sort_field = array_key_exists('sort_field', $data) ? $data["sort_field"] : 'id';
            $sort_type = array_key_exists('sort_type', $data) ? $data["sort_type"] : 'desc';
            $data = [];
            $data['id_in'] = explode(',', $ids);
            $data["sort_field"] = $sort_field;
            $data["sort_type"] = $sort_type;
        }

        $routeExport = new RouteExport(
            $this->getRepository(),
            $this->getReceiptPaymentRepository(),
            $this->getTemplateRepository(),
            $this->getTemplatePaymentRepository(),
            $this->getTemplatePaymentMappingRepository(),
            $data
        );
        $routeExport->is_update = true;
        $routeExport->is_extend = true;
        $routeExport->excelColumnConfig = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'route');
        $currentUser = $this->getCurrentUser();
        return $routeExport->exportFileTemplate($currentUser->id);
    }

    protected function _processDataImport($update = false)
    {
        $dataEx = json_decode(request()->get('data'));
        $routeImport = new RouteImport();
        $data = [];
        $excelColumnConfig = $this->getExcelColumnConfigRepository()->getColumnConfig(null, 'route');
        foreach ($dataEx as $index => $row) {
            $rowMap = $routeImport->map($row, $excelColumnConfig, $dataEx);
            if ($index > 1) {
                $rowMap['importable'] = true;
                $rowMap['failures'] = [];
                $data[] = $rowMap;
            }
        }

        $this->getRepository()->getValidator()->validateImportUpdate($data);
        $errors = $this->getRepository()->getValidator()->errorsBag();

        $routeUpdates = [];
        $routeList = $this->getRepository()->getRouteByRouteCodes(array_column($data, 'route_code'));
        foreach ($routeList as $item) {
            $routeUpdates[$item->route_code] = $item;
        }

        $listReceiptPayment = $this->getReceiptPaymentRepository()->getAllExcel();
        $receipts = [];
        foreach ($listReceiptPayment as $receipt) {
            $receipts[mb_strtoupper(Str::slug($receipt->name))] = $receipt->id;
        }

        foreach ($data as $key => &$row) {
            if (isset($routeUpdates[$row['route_code']])) {
                $routeEntity = $routeUpdates[$row['route_code']];
                if ($routeEntity && $routeEntity->is_lock == 1) {
                    $row['importable'] = false;
                    $row['failures'][] = 'Chuyến xe đang được khoá. Bạn không được phép cập nhật';
                    $row['error']['route_code'] = 'Chuyến xe đang được khoá. Bạn không được phép cập nhật';
                }
            }
            if (isset($row['costs'])) {
                foreach ($row['costs'] as $cost) {
                    if (!isset($receipts[mb_strtoupper(Str::slug($cost['name']))])) {
                        $row['failures'][] = 'Không tồn tại chi phí ' . $cost['name'];
                    }
                }
            }

            if (!empty($errors)) {
                foreach ($errors->get($key . '.*') as $message) {
                    $row['failures'][] = Arr::get($message, 0);
                }
            }
            if (empty($row['failures'])) {
                continue;
            }
            $row['importable'] = false;
        }
        $currentController = $this->getCurrentControllerName();
        $backendExcel = session(self::SESSION_EXCEL, []);
        $backendExcel[$currentController] = $data;
        $backendExcel[$currentController . '_type'] = $update;
        $backendExcel[$currentController . '_routeUpdate'] = $routeUpdates;
        session([self::SESSION_EXCEL => $backendExcel]);

        $this->setViewData([
            'excelColumnMappingConfigs' => $excelColumnConfig->excelColumnMappingConfigs,
            'entities' => $data,
        ]);
        $html = [
            'content' => $this->render('backend.route.import')->render(),
            'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
        ];
        return $html;
    }

    protected function _processFileImport()
    {
        $backendExcel = session(self::SESSION_EXCEL, array());
        $currentController = $this->getCurrentControllerName();
        $dataList = $backendExcel[$currentController];
        $update = $backendExcel[$currentController . '_type'];
        $routeUpdates = $backendExcel[$currentController . '_routeUpdate'];

        $ignoreCount = 0;
        $total = count($dataList);

        $routeList = [];
        $routeCostInsertList = [];
        $routeCostUpdateList = [];
        $routeApprovalHistoryList = [];

        $listReceiptPayment = $this->getReceiptPaymentRepository()->getAllExcel();
        $receipts = [];
        foreach ($listReceiptPayment as $receipt) {
            $receipts[mb_strtoupper(Str::slug($receipt->name))] = $receipt->id;
        }
        foreach ($dataList as $data) {
            if (!$data['importable']) {
                $ignoreCount++;
                continue;
            }
            try {
                $routeEntity = $routeUpdates[$data['route_code']];
                $route = [];
                if (isset($data['costs'])) {
                    $listCost = [];
                    foreach ($data['costs'] as $cost) {
                        if (isset($receipts[mb_strtoupper(Str::slug($cost['name']))])) {
                            $receiptId = $receipts[mb_strtoupper(Str::slug($cost['name']))];
                            $listCost[$receiptId] = $cost;
                        } /*else {
                            $receiptPaymentEntity = $this->getReceiptPaymentRepository()->findFirstOrNew([]);
                            $receiptPaymentEntity->type = 2;
                            $receiptPaymentEntity->is_system = 0;
                            $receiptPaymentEntity->name = $cost['name'];
                            $receiptPaymentEntity->save();
                            $listCost[$receiptPaymentEntity->id] = $cost;
                        }*/
                    }

                    //Lấy chi phí hiện tại
                    $routeCostMap = [];
                    $routeCostEntities = $routeEntity->costs;
                    foreach ($routeCostEntities as $routeCostEntity) {
                        $routeCost = [];
                        $routeCost['id'] = $routeCostEntity->recept_payment_id;
                        $routeCost['amount_driver'] = $routeCostEntity->amount_driver;
                        $routeCost['amount'] = $routeCostEntity->amount;
                        $routeCostMap[$routeCostEntity->receipt_payment_id] = $routeCost;
                    }

                    $approvedNote = $data['approved_note'];
                    $totalAmount = 0;
                    foreach ($listCost as $receiptPaymentId => $cost) {
                        $amountDriver = !empty($cost['amount_driver']) && is_numeric($cost['amount_driver']) ? $cost['amount_driver'] : 0;
                        $amountFinal = !empty($cost['amount']) && is_numeric($cost['amount']) ? $cost['amount'] : 0;

                        $routeCost = [];
                        $flagInsert = false;
                        if (isset($routeCostMap[$receiptPaymentId])) {
                            if ($amountDriver == 0 && $amountFinal == 0)
                                continue;

                            $routeCost = $routeCostMap[$receiptPaymentId];
                        } else {
                            if ($amountDriver != 0 || $amountFinal != 0) {
                                $routeCost['route_id'] = $routeEntity->id;
                                $routeCost['receipt_payment_id'] = $receiptPaymentId;
                                $routeCost['amount_driver'] = $amountDriver;
                                $routeCost['amount'] = $amountFinal;
                                $routeCostMap[$receiptPaymentId] = $routeCost;
                                $flagInsert = true;
                            }
                        }

                        if (!empty($routeCost)) {
                            if ($data['is_approved'] == config('constant.DA_PHE_DUYET')) {
                                $routeCost['amount_driver'] = $routeCost['amount_driver'] != 0 && $amountDriver == 0 ? $routeCost['amount_driver'] : $amountDriver;
                                $routeCost['amount'] = $routeCost['amount'] != 0 && $amountFinal == 0 ? $routeCost['amount'] : $amountFinal;
                                $totalAmount += $routeCost['amount'] ? $routeCost['amount'] : 0;
                            } else {
                                $routeCost['amount_driver'] = $routeCost['amount_driver'] != 0 && $amountDriver == 0 ? $routeCost['amount_driver'] : $amountDriver;
                                $routeCost['amount'] = 0;
                            }

                            if ($flagInsert)
                                $routeCostInsertList[] = $routeCost;
                            else
                                $routeCostUpdateList[] = $routeCost;
                        }
                    }

                    if ($data['is_approved'] == config('constant.DA_PHE_DUYET')) {
                        $route['final_cost'] = $totalAmount;
                        $route['is_approved'] = config('constant.DA_PHE_DUYET');
                        $route['approved_id'] = Auth::User()->id;
                        $route['approved_date'] = now();
                        $route['approved_note'] = $approvedNote;

                        //Lưu history
                        $routeApprovalHistory = [];
                        $routeApprovalHistory['route_id'] = $routeEntity->id;
                        $routeApprovalHistory['approved_id'] = Auth::User()->id;
                        $routeApprovalHistory['approved_date'] = now();
                        $routeApprovalHistory['approved_note'] = $approvedNote;
                        $routeApprovalHistoryList[] = $routeApprovalHistory;

                    } else {
                        $route['final_cost'] = $totalAmount;
                        $route['is_approved'] = config('constant.CHUA_PHE_DUYET');
                    }

                    $route['id'] = $routeEntity->id;
                    $routeList[] = $route;
                }
            } catch (Exception $e) {
                $ignoreCount++;
                logError($e . '- Data : ' . json_encode($data));
            }
        }

        try {
            DB::beginTransaction();

            //Cập nhật chuyến
            if (!empty($routeList)) {
                $routeInstance = new Routes();
                Batch::update($routeInstance, $routeList, 'id');
            }

            //Lưu lịch sử phê duyệt
            if (!empty($routeApprovalHistoryList)) {
                RouteApprovalHistory::insert($routeApprovalHistoryList);
            }

            //Cập nhật chi phí cho chuyến
            if (!empty($routeCostInsertList)) {
                RouteCost::insert($routeCostInsertList);
            }
            if (!empty($routeCostUpdateList)) {
                $routeCostInstance = new RouteCost();
                Batch::update($routeCostInstance, $routeCostUpdateList, 'id');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $ignoreCount = $total;
            logError($e);
        }

        unset($backendExcel[$currentController]);
        unset($backendExcel[$currentController . '_type']);
        session([self::SESSION_EXCEL => $backendExcel]);
        $this->setViewData([
            'total' => $total,
            'done' => $total - $ignoreCount,
        ]);

        $file = request()->file;
        if (!empty($file)) {
            app('App\Http\Controllers\Backend\FileController')->uploadImportFile($file, $total, $ignoreCount, $update, $this->getTitle());
        }

        $html = [
            'label' => $this->render('layouts.backend.elements.excel._import_label')->render(),
            'content' => $this->render('layouts.backend.elements.excel._import_result_done')->render(),
        ];
        return $html;
    }

    //Xuất biểu mẫu custom
    //CreatedBy nlhoang 10/4/2020
    public function exportCustomTemplate()
    {
        $ids = Request::get('ids');
        $templateId = Request::get('templateId');

        $arr = explode(",", $ids);

        $results = [];
        $template = $this->getTemplateRepository()->getTemplateByTemplateId($templateId);
        $data = $this->getRepository()->getExportByIDs($arr, $template);
        foreach ($data as $item) {
            $results[] = [
                'id' => $item->{'id'},
                'name' => $item->{'route_code'},
                'data' => $item
            ];
        }
        $dataExport = new TemplateExport(
            $this->getTemplateRepository(),
            $results
        );
        return $dataExport->exportCustomTemplate($templateId);
    }

    // Màn hình liệt kê đơn hàng tính giá chuyến xe
    // CreatedBy nlhoang 01/07/2020
    public function pricePolicy($id)
    {
        /** @var Routes $entity */
        $entity = $this->getRepository()->find($id);
        if (empty($entity)) {
            return $this->_backToIndex();
        }
        $customers = $this->getRepository()->getOrdersGroupByCustomerById($id);
        $this->setViewData([]);
        $this->setViewData([
            'customers' => $customers,
            'entity' => $entity
        ]);

        $html = [
            'content' => $this->render('backend.route._price_policy')->render(),
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    // Tính giá theo báo giá
    // CreatedBy nlhoang 01/07/2020
    public function calcPrice(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'route_id' => 'required',
                'customer_id' => 'required',
                'price_policy_id' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => 400,
                    'errorMessage' => $validation->messages()
                ]);
            }

            $routeId = Request::get('route_id');
            $customerId = Request::get('customer_id');
            $pricePolicyId = Request::get('price_policy_id');
            $params = [
                'routeId' => $routeId,
                'customerId' => $customerId,
                'pricePolicyId' => $pricePolicyId,
            ];
            $data = $this->getRepository()->calcPrice($params);

            return response()->json([
                'errorCode' => 200,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => 400,
                    'errorMessage' => $exception
                ]
            );
        }
    }

    // Lưu doanh thu chuyến xe
    // CreatedBy nlhoang 01/07/2020
    public function calcRevenue(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'routeId' => 'required',
                'data' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => 400,
                    'errorMessage' => $validation->messages()
                ]);
            }

            $params = $request->all();
            $data = $params['data'];

            DB::beginTransaction();
            try {
                $amount = 0;
                foreach ($data as $key => $t) {
                    foreach ($t["orders"] as $key => $o) {
                        $order = $this->getOrderRepository()->getItemById($o["orderId"]);
                        $order->amount = $o["amount"];
                        $amount += isset($o["amount"]) ? $o["amount"] : 0;

                        $order->ETD_date = empty($order->ETD_date) ? null : AppConstant::convertDate($order->ETD_date, 'Y-m-d');
                        $order->ETA_date = empty($order->ETA_date) ? null : AppConstant::convertDate($order->ETA_date, 'Y-m-d');

                        $order->ETD_date_reality = empty($order->ETD_date_reality) ? null : AppConstant::convertDate($order->ETD_date_reality, 'Y-m-d');
                        $order->ETA_date_reality = empty($order->ETA_date_reality) ? null : AppConstant::convertDate($order->ETA_date_reality, 'Y-m-d');
                        $order->order_date = empty($order->order_date) ? null : AppConstant::convertDate($order->order_date, 'Y-m-d');
                        $order->date_collected_documents = empty($order->date_collected_documents) ? null : AppConstant::convertDate($order->date_collected_documents, 'Y-m-d');
                        $order->time_collected_documents = empty($order->time_collected_documents) ? null : $order->time_collected_documents;
                        $order->date_collected_documents_reality = empty($order->date_collected_documents_reality) ? null : AppConstant::convertDate($order->date_collected_documents_reality, 'Y-m-d');
                        $order->time_collected_documents_reality = empty($order->time_collected_documents_reality) ? null : $order->time_collected_documents_reality;

                        $order->save();
                    }
                }
                $id = $params['routeId'];
                Routes::where('id', '=', $id)->update(array('price_quote_amount' => $amount));
                DB::commit();
            } catch (Exception $e) {

                DB::rollBack();
                return response()->json([
                    'errorCode' => 400,
                    'errorMessage' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'errorCode' => 200,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => 400,
                    'errorMessage' => $exception->getMessage()
                ]
            );
        }
    }

    // Màn hình liệt kê đơn hàng tính lương tài xế
    // CreatedBy nlhoang 22/07/2020
    public function payroll($id)
    {
        /** @var Routes $entity */
        $entity = $this->getRepository()->find($id);
        if (empty($entity)) {
            return $this->_backToIndex();
        }
        $customers = $this->getRepository()->getOrdersGroupByCustomerById($id);
        $this->setViewData([]);
        $this->setViewData([
            'customers' => $customers,
            'entity' => $entity
        ]);

        $html = [
            'content' => $this->render('backend.route._pay_roll')->render(),
        ];

        $this->setData($html);
        return $this->renderJson();
    }

    // Tính giá theo lương khoán
    // CreatedBy nlhoang 22/07/2020
    public function calcPayroll(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'route_id' => 'required',
                'customer_id' => 'required',
                'payroll_id' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => 400,
                    'errorMessage' => $validation->messages()
                ]);
            }

            $routeId = Request::get('route_id');
            $customerId = Request::get('customer_id');
            $payrollId = Request::get('payroll_id');
            $params = [
                'routeId' => $routeId,
                'customerId' => $customerId,
                'payrollId' => $payrollId,
            ];
            $data = $this->getRepository()->calcPayroll($params);

            return response()->json([
                'errorCode' => 200,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => 400,
                    'errorMessage' => $exception
                ]
            );
        }
    }

    // Lưu lương tài xế vào bảng tài xế
    // CreatedBy nlhoang 22/07/2020
    public function savePayroll(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'routeId' => 'required',
                'data' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => 400,
                    'errorMessage' => $validation->messages()
                ]);
            }

            $params = $request->all();
            $data = $params['data'];
            $id = $params['routeId'];
            DB::beginTransaction();
            try {
                $amount = 0;
                foreach ($data as $key => $t) {
                    foreach ($t["orders"] as $key => $o) {
                        $amount += isset($o["amount"]) ? $o["amount"] : 0;
                    }
                }
                Routes::where('id', '=', $id)->update(array('payroll_amount' => $amount));
                DB::commit();
            } catch (Exception $e) {

                DB::rollBack();
                return response()->json([
                    'errorCode' => 400,
                    'errorMessage' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'errorCode' => 200,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => 400,
                    'errorMessage' => $exception->getMessage()
                ]
            );
        }
    }

    // Tính toán khả năng tải của xe thông qua route id
    public function calcCapacity()
    {
        $routeId = Request::get('id', null);
        $vehicleId = Request::get('vehicle_id', null);

        $message = [];
        if (!empty($routeId)) {
            $message = $this->_calcByRouteId();
        }

        if (!empty($vehicleId)) {
            $message = $this->_calcByVehicleId();
        }

        $this->setViewData([
            'messages' => $message
        ]);

        $this->setData([
            'status' => empty($message) ? 'OK' : 'WARNING',
            'message' => $this->render('backend.route._warning_message')->render()
        ]);
        return $this->renderJson();
    }

    protected function _calcByRouteId()
    {
        $routeId = Request::get('id', null);
        $weight = Request::get('weight', null);
        $volume = Request::get('volume', null);
        $orderId = Request::get('order_id', null);

        $route = $this->getRepository()->search(['id_eq' => $routeId])->with('vehicle')->first();

        if (empty($route) || empty($route->vehicle)) {
            return [];
        }

        $orders = $route->orders;
        $vehicle = $route->vehicle;

        $totalWeight = 0;
        $totalVolume = 0;
        $message = [];
        foreach ($orders as $order) {
            if (!empty($orderId) && $order->id == $orderId) continue;
            $totalWeight += isset($order->weight) && is_numeric($order->weight) ? $order->weight : 0;
            $totalVolume += isset($order->volume) && is_numeric($order->volume) ? $order->volume : 0;
        }

        if (!empty($vehicle->weight) && ($weight + $totalWeight) > $vehicle->weight) {
            $message[] = trans('validation.out_of_weight', [
                'weight' => numberFormat($weight + $totalWeight),
                'vehicle' => numberFormat($vehicle->weight)
            ]);
        }

        if (!empty($vehicle->volume) && ($volume + $totalVolume) > $vehicle->volume) {
            $message[] = trans('validation.out_of_volume', [
                'volume' => numberFormat($volume + $totalVolume),
                'vehicle' => numberFormat($vehicle->volume)
            ]);
        }

        return $message;
    }

    protected function _calcByVehicleId()
    {
        $vehicleId = Request::get('vehicle_id', null);
        $orderIds = Request::get('order_ids', []);
        $vehicle = $this->getVehicleRepository()->search(['id_eq' => $vehicleId])->first();

        if (empty($vehicle) || empty($orderIds)) {
            return [];
        }

        $orders = $this->getOrderRepository()->search(['id_in' => $orderIds])->get();

        $totalWeight = 0;
        $totalVolume = 0;
        $message = [];
        foreach ($orders as $order) {
            if (isset($order->weight) && is_numeric($order->weight)) {
                $totalWeight += $order->weight;
            }

            if (isset($order->volume) && is_numeric($order->volume)) {
                $totalVolume += $order->volume;
            }
        }

        if (!empty($vehicle->weight) && $totalWeight > $vehicle->weight) {
            $message[] = trans('validation.out_of_weight', [
                'weight' => numberFormat($totalWeight),
                'vehicle' => numberFormat($vehicle->weight)
            ]);
        }

        if (!empty($vehicle->volume) && $totalVolume > $vehicle->volume) {
            $message[] = trans('validation.out_of_volume', [
                'volume' => numberFormat($totalVolume),
                'vehicle' => numberFormat($vehicle->volume)
            ]);
        }

        return $message;
    }

    //API lấy xe-tài xế của chuyến
    function getVehicleDriverByRoute()
    {
        $data = [];
        try {

            $routeId = empty(Request::get('routeId')) ? null : Request::get('routeId');
            $route = $this->getRepository()->getItemById($routeId);
            $data = [
                'vehicle' => $route && $route->vehicle ? $route->vehicle : [],
                'driver' => $route && $route->driver ? $route->driver : [],
            ];

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => $data
            ]);
        } catch (Exception $e) {
            logError($e);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    //Tạo file pdf lệnh vận chuyển
    // CreatedBy nlhoang 26/08/2020
    function shippingOrder($id)
    {
        $entity = $this->getRepository()->getItemById($id);
        $data['entity'] = $entity;

        $infos = $this->getSystemConfigRepository()->where('key', 'like', 'company.%')->get();
        $companyName = $infos
            ->filter(function ($value, $key) {
                return $value->key == "company.name";
            });
        $data['companyName'] = $companyName->isEmpty() ? 'Công ty ABC' : $companyName->first()->value;
        $companyAddress = $infos
            ->filter(function ($value, $key) {
                return $value->key == "company.address";
            });
        $data['companyAddress'] = $companyAddress->isEmpty() ? 'Việt Nam' : $companyAddress->first()->value;

        $companyMobileNo = $infos
            ->filter(function ($value, $key) {
                return $value->key == "company.mobile_no";
            });
        $data['companyMobileNo'] = $companyMobileNo->isEmpty() ? '0999.999.999' : $companyMobileNo->first()->value;

        $companyStamp = $infos
            ->filter(function ($value, $key) {
                return $value->key == "company.stamp";
            })->first();

        $stamp_path = '';
        if (!empty($companyStamp)) {
            $file = File::where('file_id', $companyStamp->value)->first();
            $stamp_path = empty($file) ? '' : public_path($file->path);
        }
        $data['companyStampPath'] = $stamp_path;

        $pdf = PDF::loadView('backend.route.shipping_order', $data)->setPaper('a4')->setWarnings(false);

        return $pdf->stream('LENH_VAN_CHUYEN.pdf')->header('Content-Type', 'application/pdf');
    }
}
