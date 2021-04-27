<?php

namespace App\Http\Controllers\Backend;

use App\Exports\QuotaExport;
use App\Exports\TemplateExport;
use App\Http\Controllers\Base\BackendController;
use App\Imports\QuotaImport;
use App\Model\Base\NestedSetBase;
use App\Model\Entities\Quota;
use App\Model\Entities\ReceiptPayment;
use App\Model\Entities\VehicleGroup;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\LocationGroupRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\QuotaCostRepository;
use App\Repositories\QuotaLocationRepository;
use App\Repositories\QuotaRepository;
use App\Repositories\LocationRepository;
use App\Repositories\ReceiptPaymentRepository;
use App\Repositories\RoutesRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\VehicleGroupRepository;
use App\Repositories\VehicleRepository;
use App\Services\RouteService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class QuotaController extends BackendController
{
    private $_locations;
    private $_costs;
    private $_updateRoute;
    protected $_locationRepository;
    protected $_provinceRepository;
    protected $_quotaLocationRepository;
    protected $_quotaCostRepository;
    protected $_vehicleGroupRepository;
    protected $_vehicleRepository;
    protected $_routeRepository;
    protected $_receiptPaymentRepository;
    protected $_templateRepository;
    protected $_columnConfigRepository;
    protected $_locationGroupRepository;
    protected $_routeService;


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
     * @return mixed
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
     * @return QuotaLocationRepository
     */
    public function getQuotaLocationRepository()
    {
        return $this->_quotaLocationRepository;
    }

    /**
     * @param $quotaLocationRepository
     */
    public function setQuotaLocationRepository($quotaLocationRepository): void
    {
        $this->_quotaLocationRepository = $quotaLocationRepository;
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
     * @return null
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
     * @return VehicleGroupRepository
     */
    public function getVehicleGroupRepository()
    {
        return $this->_vehicleGroupRepository;
    }

    /**
     * @param $vehicleGroupRepository
     */
    public function setVehicleGroupRepository($vehicleGroupRepository): void
    {
        $this->_vehicleGroupRepository = $vehicleGroupRepository;
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
     * @return null
     */
    public function getRouteRepository()
    {
        return $this->_routeRepository;
    }

    /**
     * @param $routeRepository
     */
    public function setRouteRepository($routeRepository): void
    {
        $this->_routeRepository = $routeRepository;
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
    public function setReceiptPaymentRepository($receiptPaymentRepository): void
    {
        $this->_receiptPaymentRepository = $receiptPaymentRepository;
    }


    /**
     * @param TemplateRepository $templateRepository
     */
    public function setTemplateRepository(TemplateRepository $templateRepository)
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
     * @return mixed
     */
    public function getLocationGroupRepository()
    {
        return $this->_locationGroupRepository;
    }

    /**
     * @param mixed $locationGroupRepository
     */
    public function setLocationGroupRepository($locationGroupRepository): void
    {
        $this->_locationGroupRepository = $locationGroupRepository;
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
        QuotaRepository $quotaRepository,
        LocationRepository $locationRepository,
        ProvinceRepository $provinceRepository,
        QuotaLocationRepository $quotaLocationRepository,
        QuotaCostRepository $quotaCostRepository,
        ColumnConfigRepository $columnConfigRepository,
        VehicleGroupRepository $vehicleGroupRepository,
        VehicleRepository $vehicleRepository,
        RoutesRepository $routesRepository,
        ReceiptPaymentRepository $receiptPaymentRepository,
        TemplateRepository $templateRepository,
        LocationGroupRepository $locationGroupRepository,
        RouteService $routeService
    )
    {
        parent::__construct();
        $this->setRepository($quotaRepository);
        $this->setBackUrlDefault('quota.index');
        $this->setConfirmRoute('quota.confirm');
        $this->setMenu('quota');
        $this->setTitle(trans('models.quota.name'));

        $this->setLocationRepository($locationRepository);
        $this->setProvinceRepository($provinceRepository);
        $this->setQuotaLocationRepository($quotaLocationRepository);
        $this->setQuotaCostRepository($quotaCostRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setVehicleGroupRepository($vehicleGroupRepository);
        $this->setVehicleRepository($vehicleRepository);
        $this->setRouteRepository($routesRepository);
        $this->setReceiptPaymentRepository($receiptPaymentRepository);
        $this->setTemplateRepository($templateRepository);
        $this->setLocationGroupRepository($locationGroupRepository);
        $this->setRouteService($routeService);

        $this->setMap(true);
        $this->setAuditing(true);
        $this->setExcel(true);
        $this->setExcelUpdate(true);
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

            //Nếu là danh mục dạng cha con thì xử lý lưu bằng Baum
            if ($entity instanceof NestedSetBase) {
                $this->_insertNestedSet($entity);
            } else {
                $this->beforeSave($entity);
                $entity->save();
            }
            $this->_saveRelations($entity);
            $this->_processCreateRelation($entity, $this->_locations, $this->_costs);
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

            if ($entity instanceof NestedSetBase) {
                $this->_updateNestedSet($entity);
            } else {
                $this->beforeSave($entity);
                $entity->save();
            }

            $this->_saveRelations($entity, 'edit');
            $this->_processCreateRelation($entity, $this->_locations, $this->_costs, true);

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
        //Xóa location của quota
        $this->getQuotaLocationRepository()->deleteWhere([
            'quota_id' => $entity->id
        ]);
        //Xóa cost của quota
        $this->getQuotaCostRepository()->deleteWhere([
            'quota_id' => $entity->id
        ]);
    }

    /**
     * @param Quota $entity
     * @param bool $isUpdate
     * @return mixed
     */
    public function _processCreateRelation($entity, $locations, $costs, $isUpdate = false)
    {
        //Lưu location của quota
        $this->getQuotaLocationRepository()->deleteWhere([
            'quota_id' => $entity->id
        ]);
        $location_destination_id = 0;
        $locationDestinationGroupId = 0;
        $location_arrival_id = 0;
        $locationArrivalGroupId = 0;
        if ($locations) {
            foreach ($locations as $index => $location) {
                if ($location['location_id']) {
                    $quotaLocationEntity = $this->getQuotaLocationRepository()->findFirstOrNew([]);
                    $quotaLocationEntity->quota_id = $entity->id;
                    $quotaLocationEntity->location_id = $location['location_id'];
                    $quotaLocationEntity->location_title = $location['location_title'];
                    $quotaLocationEntity->location_order = $index;
                    $quotaLocationEntity->save();
                    if ($index == 0) {
                        $location_destination_id = $location['location_id'];
                        $locationDestinationGroupId = isset($location['location']['group']['id']) ?
                            $location['location']['group']['id'] : 0;
                    } else if ($index == count($locations) - 1) {
                        $location_arrival_id = $location['location_id'];
                        $locationArrivalGroupId = isset($location['location']['group']['id']) ?
                            $location['location']['group']['id'] : 0;
                    }
                }
            }
        }

        //Lưu cost của quota
        $totalCost = 0;
        $dataCost = [];
        if ($costs) {
            foreach ($costs as $cost) {
                if (empty($cost['amount']) || empty($cost['receipt_payment_id'])) continue;

                if (array_key_exists($cost['receipt_payment_id'], $dataCost)) {
                    $dataCost[$cost['receipt_payment_id']]['amount'] += (float)$cost['amount'];
                } else {
                    $dataCost[$cost['receipt_payment_id']] = [
                        'receipt_payment_id' => $cost['receipt_payment_id'],
                        'receipt_payment_name' => isset($cost['receipt_payment_name']) ? $cost['receipt_payment_name'] : '',
                        'amount' => (float)$cost['amount']
                    ];
                }
                $totalCost += (float)$cost['amount'];
            }
        }
        $entity->costList()->detach();
        $entity->costList()->syncWithoutDetaching($dataCost);

        $entity->total_cost = $totalCost;
        $entity->title = $this->getTitleFromLocations($locations);
        $entity->location_ids = $this->getIdsFromLocations($locations);
        $entity->location_destination_id = $location_destination_id;
        $entity->location_arrival_id = $location_arrival_id;
        if ($locationDestinationGroupId != 0)
            $entity->location_destination_group_id = $locationDestinationGroupId;
        if ($locationArrivalGroupId != 0)
            $entity->location_arrival_group_id = $locationArrivalGroupId;
        $entity->save();

        //Cập nhật cost cho chuyến xe
        if ($isUpdate && $this->_updateRoute)
            $this->getRouteService()->_processRouteFromQuota($entity->id, $costs);

        return $entity;
    }

    public function findFirsOrNewQuotaByLocation($location_destination_id, $location_destination_title, $location_arrival_id, $location_arrival_title, $vehicle_group_id)
    {
        $location_destination_group_id = 0;
        $location_destination_group_title = "";
        $location_arrival_group_id = 0;
        $location_arrival_group_title = "";
        $locationDestination = $this->getLocationRepository()->getItemById($location_destination_id);
        if ($locationDestination) {
            $location_destination_group_id = $locationDestination->location_group_id;
            $location_destination_group_title = $locationDestination->group ? $locationDestination->group->title : "";
        }

        $locationArrival = $this->getLocationRepository()->getItemById($location_arrival_id);
        if ($locationArrival) {
            $location_arrival_group_id = $locationArrival->location_group_id;
            $location_arrival_group_title = $locationArrival->group ? $locationArrival->group->title : "";
        }

        $quota = $this->getRepository()->getQuotaByLocation($location_destination_id, $location_arrival_id, $vehicle_group_id,
            $location_destination_group_id, $location_arrival_group_id);
        if (empty($quota) && !empty($location_destination_id) && !empty($location_arrival_id)) {
            $quota = $this->getRepository()->findFirstOrNew([]);
            $systemCode = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_quota'), null, true);
            $quota->quota_code = $systemCode;
            $quota->location_destination_group_id = $location_destination_group_id;
            $quota->location_arrival_group_id = $location_arrival_group_id;

            $vehicle_group_name = "";
            $vehicleGroup = $this->getVehicleGroupRepository()->getVehicleGroupWithId($vehicle_group_id);
            if ($vehicleGroup)
                $vehicle_group_name = $vehicleGroup->name;

            $quotaName = (!empty($vehicle_group_name) ? $vehicle_group_name . " - " : "")
                . (!empty($location_destination_group_title) ? $location_destination_group_title : $location_destination_title) . " - "
                . (!empty($location_arrival_group_title) ? $location_arrival_group_title : $location_arrival_title);

            if (trim($quotaName) == "-")
                $quota->name = $systemCode;
            else
                $quota->name = $quotaName;

            $quota->vehicle_group_id = $vehicle_group_id;
            $quota->save();
            $locations = [
                ['location_id' => $location_destination_id, 'location_title' => $location_destination_title],
                ['location_id' => $location_arrival_id, 'location_title' => $location_arrival_title]
            ];

            $quota = $this->_processCreateRelation($quota, $locations, null);
        }
        return $quota;
    }

    public function _prepareForm()
    {
        $this->setViewData([
            'provinceList' => $this->getProvinceRepository()->getListForSelect(),
            'districtList' => [],
            'wardList' => [],
        ]);
    }

    public function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_quota'));

        $this->setViewData([
            'vehicle_groups' => VehicleGroup::getNestedList('name', 'id', ''),
            'urlTemplate' => route('quota.exportTemplate'),
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }

    protected function _prepareAfterSetEntity($prepare)
    {
        if ($prepare instanceof RedirectResponse) {
            return;
        }
        $this->setViewData([
            'receiptPayments' => ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true)
        ]);
    }

    protected function _prepareCreate()
    {
        $this->setViewData([
            'receiptPayments' => ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true),
        ]);
        return parent::_prepareCreate();
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('quota_code', $attributes)) {
            $code = $attributes['quota_code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_quota'));
            }
        }

        //Lấy danh sách location của quota
        $locations = null;
        if (array_key_exists('locations', $attributes)) {
            $locations = $attributes['locations'];
        } else if ($id != -1) {
            $locations = $this->getQuotaLocationRepository()->getLocations($id);
            if ($locations)
                $locations = json_encode($locations);
        }

        //Lấy danh sách cost của quota
        $costs = null;
        $defaultCost = true;
        if (array_key_exists('costs', $attributes)) { // Khi back lại từ confirm
            $costs = $attributes['costs'];
            $defaultCost = false;
        } else if ($id != -1) { //Sửa định mức
            $costs = $this->getQuotaCostRepository()->getCosts($id);
            if ($costs && count($costs) > 0) {
                $costs = json_encode($costs);
                $defaultCost = false;
            }
        }
        if ($defaultCost == true) { //Tạo mới định mức
            $receiptPayments = ReceiptPayment::getScopedNestedList('name', 'id', '-', 2, true);
            $costs = [];
            foreach ($receiptPayments as $receiptPayment) {
                $cost['receipt_payment_id'] = explode("_", $receiptPayment)[0];
                $cost['receipt_payment_name'] = explode("_", $receiptPayment)[1];
                $cost['amount'] = 0;
                $costs[] = $cost;
            }
            $costs = json_encode($costs);
        }

        //Get group vehicle list
        $vehicle_group_list = $this->getVehicleGroupRepository()->getNestedList('name', 'id', '');

        $this->setViewData([
            'code' => $code,
            'locations' => $locations,
            'costs' => $costs,
            'vehicle_group_list' => $vehicle_group_list
        ]);
    }

    protected function _prepareConfirm()
    {
        $entity = $this->_getFormData();

        //Lấy danh sách location của quota
        $locations = json_decode($entity->locations, true);
        if ($locations)
            foreach ($locations as $index => $location) {
                if (!isset($location['location_id'])) {
                    unset($locations[$index]);
                }
            }

        //Lấy danh sách cost của quota
        $costs = json_decode($entity->costs, true);
        $totalCost = 0;
        $dataCost = [];
        if ($costs) {
            foreach ($costs as $index => $cost) {
                if (empty($cost['amount']) || empty($cost['receipt_payment_id'])) continue;

                if (array_key_exists($cost['receipt_payment_id'], $dataCost)) {
                    $dataCost[$cost['receipt_payment_id']]['amount'] += (float)$cost['amount'];
                } else {
                    $dataCost[$cost['receipt_payment_id']] = [
                        'receipt_payment_id' => $cost['receipt_payment_id'],
                        'receipt_payment_name' => $cost['receipt_payment_name'],
                        'amount' => (float)$cost['amount']
                    ];
                }
                $totalCost += (float)$cost['amount'];
            }
        }

        $entity->locations = $locations;
        $entity->costs = $dataCost;
        $entity->total_cost = $totalCost;
        $this->setEntity($entity);
        $this->setViewData([
            'show_history' => false,
        ]);
    }

    protected function _prepareShow($id)
    {
        $entity = $this->getRepository()->findWithRelation($id);

        //Lấy danh sách location của quota
        $locations = $this->getQuotaLocationRepository()->getLocations($id);
        if ($locations)
            $locations = $locations->toArray();

        //Lấy danh sách cost của quota
        $costs = null;
        $costs = $this->getQuotaCostRepository()->getCosts($id);
        foreach ($costs as &$cost) {
            $cost->receipt_payment_name = $cost->receiptPayment ? $cost->receiptPayment->name : $cost->receipt_payment_name;
        }
        if ($costs)
            $costs = $costs->toArray();

        $entity->locations = $locations;
        $entity->costs = $costs;

        $this->setEntity($entity);
        $this->setViewData([
            'show_history' => true,
        ]);
    }

    protected function beforeSave($entity)
    {
        $this->_locations = json_decode($entity->locations, true);
        $this->_costs = json_decode($entity->costs, true);
        $this->_updateRoute = ($entity->update_route === 'true');
    }

    //Lấy chi phí bởi lộ trình và chủng loại xe
    public function getCostsByLocations()
    {
        $data = [];
        try {
            $locationsJson = Request::get('locations', null);
            $quotaId = Request::get('quota-id', 0);
            $vehicleGroupId = Request::get('vehicle_group_id', null);
            if ($locationsJson) {
                $locations = json_decode($locationsJson, true);
                if ($locations) {
                    $location_ids = $this->getIdsFromLocations($locations);
                    $quota = $this->getRepository()->getQuotaByLocationsAndVehicleGroup($location_ids, $vehicleGroupId, $quotaId);
                    if ($quota) {
                        $costs = $this->getQuotaCostRepository()->getCosts($quota->id);
                        if ($costs)
                            $data = json_encode($costs);
                    }
                }
            }
            return response()->json($data);
        } catch (Exception $e) {
            logError($e);
            return response()->json($data);
        }
    }

    public function getTitleFromLocations($locations)
    {
        $title = "";
        if ($locations)
            $title = implode("-", array_column($locations, 'location_title'));

        return $title;
    }

    public function getIdsFromLocations($locations)
    {
        $location_ids = "";
        if ($locations)
            $location_ids = implode("-", array_column($locations, 'location_id'));

        return $location_ids;
    }

    //API Lấy dữ liệu cho combobox định mức chi phí
    public function getDataForComboBox()
    {
        $vehicle = null;
        if (request('vehicle_id'))
            $vehicle = $this->getVehicleRepository()->getItemById(request('vehicle_id'));
        try {
            $query = Quota::select("id", "name as title", "title as routes", "total_cost");
            if ($vehicle && $vehicle->group_id) {
                $query = $query->where('quota.vehicle_group_id', '=', $vehicle->group_id);
            }
            $query = $query->where(function ($q) {
                $q->where('title', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('name', 'LIKE', '%' . request('q') . '%');
            })
                ->orderBy('title', 'asc')
                ->paginate(10);
            // return response()->json($data);

            return response()->json(['items' => $query->toArray()['data'], 'pagination' => $query->nextPageUrl() ? true : false]);
        } catch (Exception $e) {
            logError($e);
            return response()->json([]);
        }
    }

    //API Lấy danh sách chi phí bởi id định mức chi phí
    public function getCostsByQuota()
    {
        $data = [];
        try {
            $quotaId = Request::get('quota_id', null);
            $quotaCosts = '';
            $total_cost = 0;
            if ($quotaId) {
                $quotaCosts = $this->getQuotaCostRepository()->getCosts($quotaId);
                if ($quotaCosts)
                    $quotaCosts = json_encode($quotaCosts);
                $quota = $this->getRepository()->getItemById($quotaId);
                if ($quota) {
                    $total_cost = $quota->total_cost;
                }
            }
            $data = [
                'costs' => $quotaCosts,
                'total_cost' => $total_cost
            ];
            return response()->json($data);
        } catch (Exception $e) {
            logError($e);
            return response()->json($data);
        }
    }

    public function exportTemplate()
    {
        $ids = Request::get('ids', null);
        $data = $this->_getDataIndex(false);

        if (isset($ids)) {
            $data = [];
            $data['id_in'] = explode(',', $ids);
        }

        $quotaExport = new QuotaExport(
            $this->getRepository(),
            $this->getLocationRepository(),
            $this->getReceiptPaymentRepository(),
            $this->getVehicleGroupRepository(),
            $this->getLocationGroupRepository(),
            $data
        );
        $update = Request::has('update') ? true : false;
        return $quotaExport->exportFromTemplate($update);
    }

    public function _mappingDataImport($data, $update)
    {
        $numberCode = 0;

        $updateRoute = false;
        if (is_object(Arr::last($data))) {
            $updateRoute = Arr::last($data)->update_route;
            unset($data[count($data) - 1]);
        }

        $listCost = Arr::last($data);
        unset($data[count($data) - 1]);

        $quotaImport = new QuotaImport($listCost, $updateRoute);
        $listQuotaCode = [];

        foreach ($data as &$quota) {
            $quota = $quotaImport->map($quota);
            if ($quota != null && empty($quota['quota_code'])) $numberCode++;
            if ($update) {
                $listQuotaCode[] = $quota['quota_code'];
            }
        }

        $systemCodeList = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCodeForExcels(config('constant.sc_quota'), $numberCode);
        $i = 0;
        $listQuota = $update ? $this->getRepository()->search(['quota_code_in' => $listQuotaCode])->get() : null;

        foreach ($data as &$item) {
            if ($item != null) {
                if (empty($item['quota_code'])) {
                    $item['quota_code'] = $systemCodeList[$i];
                    $i++;
                }
                $item['importable'] = true;
                $item['failures'] = [];
            }

            if ($update) {
                $quotaItem = $listQuota->where('quota_code', $item['quota_code'])->first();
                if (isset($quotaItem)) {
                    $item['id'] = $quotaItem->id;
                }
            }
        }

        $this->setViewData([
            'listCost' => array_keys($quotaImport->_header)
        ]);

        return $data;
    }

    /**
     * @param Quota $entity
     * @param $data
     * @return mixed
     */
    protected function _processDataForImport($entity, $data)
    {
        $entity->save();
        $listCost = Arr::get($data, 'listCost', []);

        $result = [];
        $totalCost = 0;
        foreach ($listCost as $key => $cost) {
            if (empty($cost)) continue;

            $comboCostName = explode('|', $key);
            if (count($comboCostName) !== 2) continue;
            $result[] = [
                'receipt_payment_id' => Arr::first($comboCostName),
                'receipt_payment_name' => Arr::last($comboCostName),
                'amount' => $cost,
            ];
            $totalCost += (float)$cost;
        }
        $entity->costList()->detach();
        $entity->costList()->sync($result);

        $locationList = $this->getLocationRepository()->search(['code_in' => [$data['location_destination_id'], $data['location_arrival_id']]])
            ->get()->keyBy('code')->toArray();
        $locationGroupList = $this->getLocationGroupRepository()->search(['code_in' => [$data['location_destination_group_id'], $data['location_arrival_group_id']]])
            ->get()->keyBy('code')->toArray();

        $listTitle = [];
        $listLocationId = [];
        $resultLocation = [];
        if (isset($locationList[$data['location_destination_id']])) {
            $entity->location_destination_id = $locationList[$data['location_destination_id']]['id'];
            $entity->location_destination_group_id = $locationList[$data['location_destination_id']]['location_group_id'];
            $listTitle[] = $locationList[$data['location_destination_id']]['title'];
            $listLocationId[] = $locationList[$data['location_destination_id']]['id'];

            $resultLocation[] = [
                'location_id' => $locationList[$data['location_destination_id']]['id'],
                'location_title' => $locationList[$data['location_destination_id']]['title'],
                'location_order' => 0,
            ];
        }
        if (isset($locationGroupList[$data['location_destination_group_id']])) {
            $entity->location_destination_group_id = $locationGroupList[$data['location_destination_group_id']]['id'];
        }

        if (isset($locationList[$data['location_arrival_id']])) {
            $entity->location_arrival_id = $locationList[$data['location_arrival_id']]['id'];
            $entity->location_arrival_group_id = $locationList[$data['location_arrival_id']]['location_group_id'];
            $listTitle[] = $locationList[$data['location_arrival_id']]['title'];
            $listLocationId[] = $locationList[$data['location_arrival_id']]['id'];

            $resultLocation[] = [
                'location_id' => $locationList[$data['location_arrival_id']]['id'],
                'location_title' => $locationList[$data['location_arrival_id']]['title'],
                'location_order' => 1,
            ];
        }
        if (isset($locationGroupList[$data['location_arrival_group_id']])) {
            $entity->location_arrival_group_id = $locationGroupList[$data['location_arrival_group_id']]['id'];
        }

        if (!empty($resultLocation)) {
            $entity->locations()->detach();
            $entity->locations()->sync($resultLocation);
        }

        $vehicleGroup = $this->getVehicleGroupRepository()->search(['code_eq' => $data['vehicle_group_id']])->first();

        $entity->title = implode('-', $listTitle);
        $entity->location_ids = implode('-', $listLocationId);
        $entity->total_cost = $totalCost;
        $entity->vehicle_group_id = $vehicleGroup->id;

        //Cập nhật chi phí cho chuyến
        if (isset($data['updateRoute']) && $data['updateRoute'] == true)
            $this->getRouteService()->_processRouteFromQuota($entity->id, $result);

        return $entity;
    }

    protected function _processQuickSave($id, $field, $value)
    {
        $entity = $this->getRepository()->getItemById($id);
        if ($entity != null) {
            $entity->$field = $value;
            $entity->save();
        }
    }

    //Xuất biểu mẫu custom
    //CreatedBy nlhoang 10/4/2020
    public function exportCustomTemplate()
    {
        $ids = Request::get('ids');
        $templateId = Request::get('templateId');

        $arr = explode(",", $ids);
        $datas = [];
        foreach ($arr as $item) {
            $data = $this->getRepository()->getExportByID($item);
            $datas[] = [
                'id' => $item,
                'name' => $data->{'quota_code'},
                'data' => $data
            ];
        }

        $dataExport = new TemplateExport(
            $this->getTemplateRepository(),
            $datas
        );
        return $dataExport->exportCustomTemplate($templateId);
    }
}
