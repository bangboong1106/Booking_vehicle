<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Common\HttpCode;
use App\Exports\TemplateExport;
use App\Http\Controllers\Base\BackendController;
use App\Model\Entities\Order;
use App\Model\Entities\OrderCustomer;
use App\Model\Entities\OrderCustomerGoods;
use App\Model\Entities\OrderCustomerHistory;
use App\Model\Entities\OrderGoods;
use App\Model\Entities\VehicleGroup;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\CurrencyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DistrictRepository;
use App\Repositories\GoodsGroupRepository;
use App\Repositories\GoodsTypeRepository;
use App\Repositories\GoodsUnitRepository;
use App\Repositories\OrderCustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProvinceRepository;
use App\Repositories\LocationRepository;
use App\Repositories\VehicleGroupRepository;
use App\Repositories\WardRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\ExcelColumnConfigRepository;

use App\Services\NotificationService;
use App\Services\OrderCustomerService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class OrderCustomerController extends BackendController
{
    protected $_locationRepository;
    protected $_orderRepository;
    protected $_customerRepository;
    protected $_columnConfigRepository;
    protected $_provinceRepository;
    protected $_districtRepository;
    protected $_wardRepository;
    protected $_currencyRepository;
    protected $_vehicleGroupRepository;
    protected $_templateRepository;
    protected $_adminUserRepository;
    protected $_excelColumnConfigRepository;
    protected $_goodsUnitRepository;
    protected $_goodsGroupRepository;
    protected $_goodsTypeRepository;
    protected $_orderCustomerService;
    protected $_notificationService;

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
     * @return mixed
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
     * @return mixed
     */
    public function getAdminUserRepository()
    {
        return $this->_adminUserRepository;
    }

    /**
     * @param mixed $adminUserRepository
     */
    public function setAdminUserRepository($adminUserRepository): void
    {
        $this->_adminUserRepository = $adminUserRepository;
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
     * @return mixed
     */
    public function getGoodsGroupRepository()
    {
        return $this->_goodsGroupRepository;
    }

    /**
     * @param mixed $goodsGroupRepository
     */
    public function setGoodsGroupRepository($goodsGroupRepository): void
    {
        $this->_goodsGroupRepository = $goodsGroupRepository;
    }

    /**
     * @return mixed
     */
    public function getGoodsTypeRepository()
    {
        return $this->_goodsTypeRepository;
    }

    /**
     * @param mixed $goodsTypeRepository
     */
    public function setGoodsTypeRepository($goodsTypeRepository): void
    {
        $this->_goodsTypeRepository = $goodsTypeRepository;
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
        OrderCustomerRepository $orderCustomerRepository,
        LocationRepository $locationRepository,
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository,
        ColumnConfigRepository $columnConfigRepository,
        CurrencyRepository $currencyRepository,
        ProvinceRepository $provinceRepository,
        DistrictRepository $districtRepository,
        WardRepository $wardRepository,
        VehicleGroupRepository $vehicleGroupRepository,
        TemplateRepository $templateRepository,
        AdminUserInfoRepository $adminUserInfoRepository,
        ExcelColumnConfigRepository $excelColumnConfigRepository,
        GoodsUnitRepository $goodsUnitRepository,
        GoodsGroupRepository $goodsGroupRepository,
        GoodsTypeRepository $goodsTypeRepository,
        OrderCustomerService $orderCustomerService,
        NotificationService $notificationService
    )
    {
        parent::__construct();
        $this->setRepository($orderCustomerRepository);
        $this->setBackUrlDefault('order-customer.index');
        $this->setConfirmRoute('order-customer.confirm');
        $this->setMenu('order_customer');
        $this->setTitle(trans('models.order_customer.name'));
        $this->setLocationRepository($locationRepository);
        $this->setCustomerRepository($customerRepository);
        $this->setOrderRepository($orderRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setCurrencyRepository($currencyRepository);
        $this->setProvinceRepository($provinceRepository);
        $this->setDistrictRepository($districtRepository);
        $this->setWardRepository($wardRepository);
        $this->setVehicleGroupRepository($vehicleGroupRepository);
        $this->setTemplateRepository($templateRepository);
        $this->setAdminUserRepository($adminUserInfoRepository);
        $this->setExcelColumnConfigRepository($excelColumnConfigRepository);
        $this->setGoodsUnitRepository($goodsUnitRepository);
        $this->setGoodsGroupRepository($goodsGroupRepository);
        $this->setGoodsTypeRepository($goodsTypeRepository);
        $this->setOrderCustomerService($orderCustomerService);
        $this->setNotificationService($notificationService);

        $this->setMap(true);
        //$this->setAuditing(true);
        $this->setDeleted(true);
        //$this->setExcel(false);
        // $this->setExcelUpdate(false);
        $this->setViewData([
            'urlTemplate' => route('order-customer.exportTemplate'),
        ]);
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
            $entity->status = config('constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG');
            $entity->status_goods = config('constant.ORDER_CUSTOMER_STATUS_GOODS.HET_HANG');
            $entity->source_creation = config('constant.SOURCE_CREATE_C20_ORDER_CUSTOMER_FORM');
            $entity->save();
            $this->_saveRelations($entity);

            OrderCustomerHistory::insert([
                'order_customer_id' => $entity->id,
                'status' => $entity->status,
                'reason' => null
            ]);

            $data = $this->_getFormData(false, true);
            $this->_processCreateRelation('save', $data, $entity);
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
            $entity->save();
            $this->_saveRelations($entity, 'edit');

            $data = $this->_getFormData(false, true);
            $this->_processCreateRelation('edit', $data, $entity);
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

    protected function _validExtend($params)
    {
        $massage = '';
        $goodsList = isset($params['goods']) ? $params['goods'] : null;
        if ($goodsList) {
            $goodsTypeId = array_column($goodsList, 'goods_type_id');
            if (count($goodsTypeId) != count(array_unique($goodsTypeId)))
                $massage = "Hàng hóa không được nhập trùng";
        }
        if (!empty($massage)) {
            return ['goods_error' => $massage];
        }

        return [];

    }

    public function _deleteRelations($entity)
    {
        //xoa history
        OrderCustomerHistory::where([
            'order_customer_id' => $entity->id
        ])->delete();

        //Xoa goods
        OrderCustomerGoods::where([
            'order_customer_id' => $entity->id
        ])->delete();

        //Xoa đơn vận tải
        Order::where([
            'order_customer_id' => $entity->id
        ])->delete();

    }

    protected function _saveRelations($entity, $action = 'save')
    {
        parent::_saveRelations($entity, $action);
        $this->_saveGoods($entity);
    }

    public function _processCreateRelation($action, $data, $entity)
    {
        $orderList = $this->getOrderRepository()->getOrdersByOrderCustomerId($entity->id);
        if ($action == 'save' || !$orderList || count($orderList) == 0) {

            $this->getOrderCustomerService()->createOrderFromOrderCustomer($entity, $entity->goodsList, config('constant.SOURCE_CREATE_C20_ORDER_CUSTOMER_FORM'));

        } else {
            $this->getOrderCustomerService()->updateOrderCustomerInfo($entity, false);

            if ($orderList) {

                $goodUnits = $this->getGoodsUnitRepository()->getListByTitle()->toArray();

                $isUpdate = false;
                if (count($orderList) == 1)
                    $isUpdate = true;

                //Cập nhật hàng hóa
                $orderCustomerGoods = $entity->goods;
                $orderGoodsList = $this->getOrderRepository()->getOrderGoodsByOrderIds(array_column($orderList->toArray(), 'id'));
                $orderGoodsAfter = [];
                $orderCustomerGoodsTypeIdList = array_column($orderCustomerGoods, 'goods_type_id');

                if ($orderCustomerGoods && count($orderCustomerGoods) > 0) {
                    foreach ($orderCustomerGoods as &$goods) {
                        $isCreate = true;
                        foreach ($orderGoodsList as $oGoods) {
                            if ($oGoods->goods_type_id == $goods['goods_type_id']) {
                                $diffQuantity = $goods['quantity'] - $oGoods->quantity;
                                $goods['quantity'] = $goods['quantity'] + $diffQuantity;
                                $oGoods->quantity = $oGoods->quantity + $diffQuantity;
                                $oGoods->total_weight = $oGoods->quantity * $oGoods->weight;
                                $oGoods->total_volume = $oGoods->quantity * $oGoods->volume;
                                $oGoods->insured_goods = $goods['insured_goods'];
                                $oGoods->note = $goods['note'];
                                if ($oGoods->quantity == 0)
                                    $oGoods->delete();
                                else
                                    $oGoods->save();

                                if (isset($orderGoodsAfter[$oGoods->order_id])) {
                                    $orderGoodsAfter[$oGoods->order_id]['quantity'] = $orderGoodsAfter[$oGoods->order_id]['quantity'] + $oGoods->quantity;
                                    $orderGoodsAfter[$oGoods->order_id]['weight'] = $orderGoodsAfter[$oGoods->order_id]['weight'] + $oGoods->total_weight;
                                    $orderGoodsAfter[$oGoods->order_id]['volume'] = $orderGoodsAfter[$oGoods->order_id]['volume'] + $oGoods->total_volume;
                                } else {
                                    $orderGoodsAfter[$oGoods->order_id] = [
                                        'quantity' => $oGoods->quantity,
                                        'weight' => $oGoods->total_weight,
                                        'volume' => $oGoods->total_volume,
                                    ];
                                }
                                $isCreate = false;
                            } else {
                                //Ko có hàng hóa ở DDH thì xóa ở DHVT
                                if (!array_search($oGoods->goods_type_id, $orderCustomerGoodsTypeIdList)) {
                                    $oGoods->delete();
                                }
                            }
                        }

                        //Tạo mới nếu ko có hàng hóa ở DHVT
                        if ($isCreate) {
                            $orderGoods = new OrderGoods();
                            $orderFirst = $orderList[0];
                            $orderGoods->order_id = $orderFirst->id;
                            $orderGoods->goods_type_id = $goods['goods_type_id'];
                            $orderGoods->goods_unit_id = isset($goodUnits[$goods['goods_unit']]) ? $goodUnits[$goods['goods_unit']] : 0;
                            $orderGoods->quantity = $goods['quantity'];
                            $orderGoods->weight = $goods['weight'];
                            $orderGoods->volume = $goods['volume'];
                            $orderGoods->total_weight = $goods['total_weight'];
                            $orderGoods->total_volume = $goods['total_volume'];
                            $orderGoods->insured_goods = $goods['insured_goods'];
                            $orderGoods->note = $goods['note'];
                            $orderGoods->save();

                            if (isset($orderGoodsAfter[$orderFirst->id])) {
                                $orderGoodsAfter[$orderFirst->id]['quantity'] = $orderGoodsAfter[$orderFirst->id]['quantity'] + $oGoods->quantity;
                                $orderGoodsAfter[$orderFirst->id]['weight'] = $orderGoodsAfter[$orderFirst->id]['weight'] + $oGoods->total_weight;
                                $orderGoodsAfter[$orderFirst->id]['volume'] = $orderGoodsAfter[$orderFirst->id]['volume'] + $oGoods->total_volume;
                            } else {
                                $orderGoodsAfter[$orderFirst->id] = [
                                    'quantity' => $goods['quantity'],
                                    'weight' => $goods['weight'],
                                    'volume' => $goods['volume'],
                                ];
                            }
                        }
                    }
                } else {
                    //Xóa hêt bên DHVT nếu ko có hàng hóa bên DH
                    foreach ($orderGoodsList as $oGoods) {
                        $oGoods->delete();
                        $orderGoodsAfter[$oGoods->order_id] = 0;
                    }
                }

                foreach ($orderList as $index => $order) {
                    $order->order_no = $entity->order_no;
                    $order->customer_id = $entity->customer_id;
                    $order->customer_name = $entity->customer_name;
                    $order->customer_mobile_no = $entity->customer_mobile_no;
                    $order->order_date = $entity->order_date;
                    $order->good_details = $entity->goods_detail;

                    if (isset($orderGoodsAfter[$order->id])) {
                        $order->quantity = $orderGoodsAfter[$order->id]['quantity'];
                        $order->weight = $orderGoodsAfter[$order->id]['weight'];
                        $order->volume = $orderGoodsAfter[$order->id]['volume'];
                    }
                    $order->save();

                    $this->getOrderRepository()->updateOrderPayment(
                        $order->id,
                        $entity->payment_type,
                        $entity->payment_user_id,
                        $entity->goods_amount,
                        $entity->vat,
                        $entity->anonymous_amount);

                    //Cập nhật thông tin nhận trả nếu DHKH - DH là 1-1
                    if ($isUpdate) {
                        $order->location_destination_id = $entity->location_destination_id;
                        $order->location_arrival_id = $entity->location_arrival_id;
                        $order->ETD_date = AppConstant::convertDate($entity->ETD_date, 'Y-m-d');
                        $order->ETD_time = AppConstant::convertTime($entity->ETD_time, 'H:i');
                        $order->ETA_date = AppConstant::convertDate($entity->ETA_date, 'Y-m-d');
                        $order->ETA_time = AppConstant::convertTime($entity->ETA_time, 'H:i');
                        $this->getOrderRepository()->updateOrderLocation(
                            $order->id,
                            $entity->location_destination_id,
                            config('constant.DESTINATION'),
                            $entity->ETD_time,
                            $entity->ETD_time);
                        $this->getOrderRepository()->updateOrderLocation(
                            $order->id,
                            $entity->location_arrival_id,
                            config('constant.ARRIVAL'),
                            $entity->ETA_date,
                            $entity->ETA_time
                        );
                    }

                }
            }
        }

        //Lưu vehicle group
        $vehicleGroupArray = [];
        if (isset($data['listVehicleGroup'])) {
            $listVehicleGroups = $data['listVehicleGroup'];
            foreach ($listVehicleGroups as $vehicleGroup) {
                if (empty($vehicleGroup['vehicle_group_id'])) {
                    continue;
                }
                $vehicleGroupArray[] = [
                    'order_customer_id' => $entity->id,
                    'vehicle_group_id' => $vehicleGroup['vehicle_group_id'],
                    'vehicle_number' => $vehicleGroup['vehicle_number'],
                ];
            }
        }
        $entity->vehicleGroups = $vehicleGroupArray;
        $this->_saveVehicleGroups($entity);

        //THông báo cho chủ hàng, khách hàng
        if ($action == 'save')
            $this->getNotificationService()->notifyToCustomerAndClient($entity);

    }

    public function _prepareForm()
    {
        $vehicle_group_list = $this->getVehicleGroupRepository()->getNestedList('name', 'id', '');
        $userAdminList = $this->getAdminUserRepository()->getListForSelect();

        $goodsUnits = $this->getGoodsUnitRepository()->getListForSelect()->toArray();

//        $goodsGroup = $this->getGoodsGroupRepository()->getScopedNestedList('name', 'id', '-', false);
//        $goodsGroup = Arr::prepend($goodsGroup, '', null);

        $goodsOwners = $this->getCustomerRepository()->getGoodsOwnerList()->pluck('full_name', 'id');

        $this->setViewData([
            'customers' => $this->getCustomerRepository()->search()->get(),
            'provinceList' => $this->getProvinceRepository()->getListForSelect(),
            'districtList' => [],
            'wardList' => [],
            'currencyList' => $this->getCurrencyRepository()->getListForSelect(),
            'vehicle_group_list' => $vehicle_group_list,
            'commissionType' => config('system.order_customer_commission_type'),
            'userAdminList' => $userAdminList,
            'goodsUnits' => $goodsUnits,
            // 'goodsGroups' => $goodsGroup,
            'goodsOwners' => $goodsOwners,
            'goodsTypes' => $this->getGoodsTypeRepository()->getListForSelect(),
        ]);
    }

    protected function _prepareAfterSetEntity($prepare)
    {
        if ($prepare instanceof RedirectResponse) {
            return;
        }
        $entity = $this->getEntity();
        $this->getSelected($entity);
    }

    protected function _prepareCreate()
    {
        $parent = parent::_prepareCreate();
        $entity = $this->getEntity();
        $this->getSelected($entity);
        return $parent;
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order_customer'));
            }
        }

        $this->setViewData([
            'code' => $code,
        ]);
    }

    protected function _prepareConfirm()
    {
        $entity = $this->_getFormData();

        if (isset($entity->listVehicleGroup)) {
            $vehicleGroupData = $this->getVehicleGroupRepository()->all(['code', 'name', 'id'])->sortBy('name');
            $listVehicleGroups = $entity->listVehicleGroup;
            $result = [];
            foreach ($listVehicleGroups as $vehicleGroup) {
                if (empty($vehicleGroup['vehicle_group_id'])) {
                    continue;
                }
                $vehicleGroupEntity = $vehicleGroupData->firstWhere('id', $vehicleGroup['vehicle_group_id']);

                $result[] = [
                    'vehicle_group_id' => $vehicleGroupEntity ? $vehicleGroupEntity->id : '',
                    'vehicle_group_name' => $vehicleGroupEntity ? $vehicleGroupEntity->name : '',
                    'vehicle_number' => $vehicleGroup['vehicle_number']
                ];
            }
            $entity->listVehicleGroups = $result;
        }

        if (!empty($goods)) {
            $result = $this->getOrderCustomerService()->calcAmountEstimate($entity->location_destination_id, $entity->location_arrival_id, $entity->weight);
            $entity->amount_estimate = $result[0];
        }

        $this->setEntity($entity);

        $this->setViewData([
            'show_history' => false,
        ]);
    }

    protected function _prepareShow($id)
    {
        $entity = $this->getRepository()->findWithRelation($id);

        $entity->orders = $this->getRepository()->getOrdersByID($id);

        $entity = $this->_processInput($entity);
        $entity = $this->_processShowListVehicleGroup($entity);
        $entity = $this->_processShowListGoods($entity);

        $this->setEntity($entity);

        $this->setViewData([
            'show_history' => true
        ]);
    }

    public function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_order_customer'));
        $dayCondition = env('DAY_CONDITION_DEFAULT', 4);

        $this->setViewData([
            'dayCondition' => $dayCondition,
            'vehicle_groups' => VehicleGroup::getNestedList('name', 'id', ''),
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"],
            'userId' => $userId
        ]);
    }

    protected function _findEntityForUpdate($id)
    {
        $entity = $this->_findOrNewEntity(null, false, true);
        return $this->_processInput($entity);
    }

    protected function _findEntityForStore()
    {
        $entity = $this->_findOrNewEntity(null, false);
        return $this->_processInput($entity);
    }

    public function _processInput($entity)
    {
        $entity->order_date = empty($entity->order_date) ? null : AppConstant::convertDate($entity->order_date, 'Y-m-d');
        $entity->ETD_date = empty($entity->ETD_date) ? null : AppConstant::convertDate($entity->ETD_date, 'Y-m-d');
        $entity->ETA_date = empty($entity->ETA_date) ? null : AppConstant::convertDate($entity->ETA_date, 'Y-m-d');
        $entity->ETD_time = empty($entity->ETD_time) ? null : $entity->ETD_time;
        $entity->ETA_time = empty($entity->ETA_time) ? null : $entity->ETA_time;
        return $entity;
    }

    public function getSelected($entity)
    {
        $entity->goods = !empty($entity->goods) ? $entity->goods :
            $entity->listGoods->pluck('pivot')->toArray();
    }

    protected function _prepareEdit($id = null)
    {
        $parent = parent::_prepareEdit($id);
        $entity = $this->getEntity();
        $this->setEntity($entity);
        return $parent;
    }

    protected function _processShowListVehicleGroup($entity)
    {
        $listVehicleGroups = $entity->listVehicleGroups;
        $result = [];
        $vehicleGroupData = $this->getVehicleGroupRepository()->all(['code', 'name', 'id'])->sortBy('name');

        foreach ($listVehicleGroups as $vehicleGroup) {
            if (empty($vehicleGroup->pivot)) {
                continue;
            }
            $vehicleGroupEntity = $vehicleGroupData->firstWhere('id', $vehicleGroup->pivot->vehicle_group_id);

            $result[] = [
                'id' => $vehicleGroup->pivot->id,
                'order_customer_id' => $vehicleGroup->pivot->order_customer_id,
                'vehicle_group_id' => $vehicleGroup->pivot->vehicle_group_id,
                'vehicle_group_name' => $vehicleGroupEntity ? $vehicleGroupEntity->name : '',
                'vehicle_number' => $vehicleGroup->pivot->vehicle_number
            ];
        }
        $entity->listVehicleGroups = $result;

        return $entity;
    }

    protected function _processShowListGoods($entity)
    {
        $listGoods = $entity->listGoods;
        $result = [];
        $goodUnits = $this->getGoodsUnitRepository()->getListForSelect()->toArray();

        foreach ($listGoods as $goods) {
            if (empty($goods->pivot)) {
                continue;
            }

            $result[] = [
                'goods_type' => $goods->title,
                'quantity' => $goods->pivot->quantity,
                'quantity_out' => $goods->pivot->quantity_out,
                'goods_unit' => isset($goodUnits[$goods->pivot->goods_unit_id]) ? $goodUnits[$goods->pivot->goods_unit_id] : '-',
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
     * Lưu thông tin nhiều chủng loại xe
     * @param $entity
     */
    protected function _saveVehicleGroups($entity)
    {
        $vehicleGroups = $entity->vehicleGroups;

        if (empty($vehicleGroups)) {
            return;
        }
        $data = [];
        foreach ($vehicleGroups as $item) {
            $data[] = [
                'order_customer_id' => !empty($item['order_customer_id']) ? $item['order_customer_id'] : 0,
                'vehicle_group_id' => !empty($item['vehicle_group_id']) ? $item['vehicle_group_id'] : 0,
                'vehicle_number' => !empty($item['vehicle_number']) ? $item['vehicle_number'] : 0
            ];
        }
        $entity->listVehicleGroups()->detach();
        $entity->listVehicleGroups()->sync($data);
    }

    /**
     * Lưu thông tin nhiều hàng hóa
     * @param $entity
     */
    protected function _saveGoods($entity)
    {
        $goodUnits = $this->getGoodsUnitRepository()->getListByTitle()->toArray();

        $goods = $entity->goods;

        if (empty($goods)) {
            return;
        }
        $data = [];
        foreach ($goods as $goodsItem) {
            $data[] = [
                'goods_type_id' => !empty($goodsItem['goods_type_id']) ? intval($goodsItem['goods_type_id']) : 0,
                'quantity' => !empty($goodsItem['quantity']) ? $goodsItem['quantity'] : 0,
                'quantity_out' => !empty($goodsItem['quantity']) ? $goodsItem['quantity'] : 0,
                'goods_unit_id' => isset($goodUnits[$goodsItem['goods_unit']]) ? intval($goodUnits[$goodsItem['goods_unit']]) : 0,
                'insured_goods' => !empty($goodsItem['insured_goods']) ? $goodsItem['insured_goods'] : 0,
                'volume' => !empty($goodsItem['volume']) ? $goodsItem['volume'] : 0,
                'weight' => !empty($goodsItem['weight']) ? $goodsItem['weight'] : 0,
                'note' => !empty($goodsItem['note']) ? $goodsItem['note'] : '',
                'total_weight' => !empty($goodsItem['total_weight']) ? $goodsItem['total_weight'] : 0,
                'total_volume' => !empty($goodsItem['total_volume']) ? $goodsItem['total_volume'] : 0,
            ];
        }
        $entity->goodsList = $data;
        $entity->listGoods()->detach();
        $entity->listGoods()->sync($data);

        $result = $this->getOrderCustomerService()->calcAmountEstimate($entity->location_destination_id, $entity->location_arrival_id, $entity->weight);
        $entity->amount_estimate = $result[0];
    }

    // Xử lý lưu nhanh
    // CreatedBy nlhoang 12/04/2020
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

    /**
     * @param OrderCustomer $entity
     * @return OrderCustomer
     */
    public function _processInputData($entity)
    {
        $entity->ETD_date = empty($entity->ETD_date) ? null : AppConstant::convertDate($entity->ETD_date, 'Y-m-d');
        $entity->ETA_date = empty($entity->ETA_date) ? null : AppConstant::convertDate($entity->ETA_date, 'Y-m-d');
        $entity->order_date = empty($entity->order_date) ? null : AppConstant::convertDate($entity->order_date, 'Y-m-d');
        return $entity;
    }

    public function orderClient()
    {
        try {
            $entities = $this->getRepository()->getListForClient([]);
            $this->setViewData([
                'entities' => $entities
            ]);
            $html = [
                'content' => $this->render('backend.' . $this->getCurrentControllerName() . '.client')->render(),
            ];

            $this->setData($html);
            return $this->renderJson();
        } catch (Exception $e) {
            logError($e);
        }
    }

    public function approvedOrderClient(Request $request)
    {
        try {
            $id = \Request::get('id');
            if ($id != 0) {
                DB::beginTransaction();
                $orderCustomer = $this->getRepository()->getItemById($id);
                $orderCustomer->is_approved = config('constant.DA_PHE_DUYET');
                $orderCustomer->save();
                DB::commit();
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => [
                    'message' => 'ok'
                ]
            ]);
        } catch (Exception $exception) {
            logError($exception . ' - Data : ' . $request);
            DB::rollBack();
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }

    //Xuất biểu mẫu custom
    //CreatedBy nlhoang 28/07/2020
    public function exportCustomTemplate()
    {
        $orderIds = Request::get('ids');
        $templateId = Request::get('templateId');

        $arr = explode(",", $orderIds);
        $results = [];
        $template = $this->getTemplateRepository()->getTemplateByTemplateId($templateId);
        $data = $this->getRepository()->getExportByIDs($arr, $template);
        foreach ($data as $item) {
            $results[] = [
                'id' => $item->{'id'},
                'name' => $item->{'code'},
                'data' => $item
            ];
        }
        $dataExport = new TemplateExport(
            $this->getTemplateRepository(),
            $results
        );
        return $dataExport->exportCustomTemplate($templateId);
    }

    //Tính thời gian dự kiến
    public function calcETA(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'etd' => 'required',
                'location_destination_id' => 'required',
                'location_arrival_id' => 'required'
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            }
            $etd = Request::get('etd');
            $location_destination_id = Request::get('location_destination_id');
            $location_arrival_id = Request::get('location_arrival_id');

            DB::beginTransaction();

            $result = $this->getOrderCustomerService()->calcETA($location_destination_id, $location_arrival_id, $etd);

            DB::commit();
            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
                'data' => [
                    'eta_date' => AppConstant::convertDate($result[0], 'd-m-Y'),
                    'eta_time' => AppConstant::convertTime($result[0], 'H:i'),
                    'distance' => $result[1]
                ]
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            logError($exception . '- Data : ' . json_encode($request));
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $exception->getMessage()
                ]
            );
        }
    }

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
                'order_customer.*'
            ];
            $items = $this->getRepository()->getItemsByIds($ids, $columns, $withRelation);

            $this->setViewData([
                'items' => $items,
            ]);

            $html = [
                'content' => $this->render('backend.order_customer.update_revenue_modal')->render(),
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
}
