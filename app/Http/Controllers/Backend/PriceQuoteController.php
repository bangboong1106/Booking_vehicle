<?php

namespace App\Http\Controllers\Backend;

use App\Common\AppConstant;
use App\Exports\OrdersCustomerExport;
use App\Http\Controllers\Base\BackendController;
use App\Imports\OrderCustomerImport;
use App\Model\Entities\OrderCustomer;
use App\Model\Entities\PriceQuote;
use App\Model\Entities\PriceQuoteFormula;
use App\Model\Entities\PriceQuotePointCharge;
use App\Model\Entities\ReceiptPayment;
use App\Model\Entities\Routes;
use App\Model\Entities\VehicleGroup;
use App\Repositories\CustomerGroupRepository;
use App\Repositories\GoodsTypeRepository;
use App\Repositories\LocationGroupRepository;
use App\Repositories\PriceQuoteRepository;
use App\Repositories\PriceQuoteCustomerGroupRepository;
use App\Repositories\ColumnConfigRepository;
use App\Repositories\OrderPriceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\RoutesRepository;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Common\HttpCode;
use Validator;

class PriceQuoteController extends BackendController
{
    protected $_customerGroupRepository;
    protected $_locationGroupRepository;
    protected $_goodsTypeRepository;
    protected $_orderPriceRepository;
    protected $_orderRepository;
    protected $_routesRepository;

    /**
     * @return mixed
     */
    public function getCustomerGroupRepository()
    {
        return $this->_customerGroupRepository;
    }

    /**
     * @param mixed $customerGroupRepository
     */
    public function setCustomerGroupRepository($customerGroupRepository): void
    {
        $this->_customerGroupRepository = $customerGroupRepository;
    }

    protected $_priceQuoteCustomerGroupRepository;

    public function getPriceQuoteCustomerGroupRepository()
    {
        return $this->_priceQuoteCustomerGroupRepository;
    }

    public function setPriceQuoteCustomerGroupRepository($priceQuoteCustomerGroupRepository): void
    {
        $this->_priceQuoteCustomerGroupRepository = $priceQuoteCustomerGroupRepository;
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
     * @return OrderPriceRepository
     */
    public function getOrderPriceRepository()
    {
        return $this->_orderPriceRepository;
    }

    /**
     * @param $OrderPriceRepository
     */
    public function setOrderPriceRepository($orderPriceRepository): void
    {
        $this->_orderPriceRepository = $orderPriceRepository;
    }

    /**
     * @return OrderRepository
     */
    public function getOrderRepository()
    {
        return $this->_orderRepository;
    }

    /**
     * @param $OrderRepository
     */
    public function setOrderRepository($orderRepository): void
    {
        $this->_orderRepository = $orderRepository;
    }

    /**
     * @return RouteRepository
     */
    public function getRoutesRepository()
    {
        return $this->_routesRepository;
    }

    /**
     * @param $OrderRepository
     */
    public function setRoutesRepository($routesRepository): void
    {
        $this->_routesRepository = $routesRepository;
    }

    public function __construct(
        PriceQuoteRepository $priceQuoteRepository,
        PriceQuoteCustomerGroupRepository $priceQuoteCustomerGroupRepository,
        CustomerGroupRepository $customerGroupRepository,
        LocationGroupRepository $locationGroupRepository,
        GoodsTypeRepository $GoodsTypeRepository,
        ColumnConfigRepository $columnConfigRepository,
        OrderPriceRepository $orderPriceRepository,
        OrderRepository $orderRepository,
        RoutesRepository $routesRepository

    ) {
        parent::__construct();

        $this->setRepository($priceQuoteRepository);
        $this->setPriceQuoteCustomerGroupRepository($priceQuoteCustomerGroupRepository);
        $this->setCustomerGroupRepository($customerGroupRepository);
        $this->setLocationGroupRepository($locationGroupRepository);
        $this->setGoodsTypeRepository($GoodsTypeRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setOrderPriceRepository($orderPriceRepository);
        $this->setOrderRepository($orderRepository);
        $this->setRoutesRepository($routesRepository);



        $this->setBackUrlDefault('price-quote.index');
        $this->setConfirmRoute('price-quote.confirm');
        $this->setMenu('quota');
        $this->setTitle(trans('models.price_quote.name'));

        /*   $this->setExcel(true);
        $this->setExcelUpdate(true);
        $this->setViewData([
            'urlTemplate' => route('price-quote.exportTemplate'),
        ]);*/
    }

    protected function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_price_quote'));
        $this->setViewData([
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
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
            $entity->save();
            // $this->_saveRelations($entity);

            $data = $this->_getFormData(false, true);
            $this->_processCreateRelation($data, $entity);
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
            // $this->_saveRelations($entity, 'edit');

            $data = $this->_getFormData(false, true);
            $this->_processCreateRelation($data, $entity);
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
        //Xóa danh sách nhóm khách hàng
        $this->getPriceQuoteCustomerGroupRepository()->deleteWhere([
            'price_quote_id' => $entity->id
        ]);
        //Xóa danh sách công thức
        $entity->formular()->delete();

        //Xóa danh sách phí rớt điểm
        $entity->pointCharges()->delete();
    }

    public function _processCreateRelation($data, $entity)
    {
        //Lưu danh sách nhóm KH
        $entity->customerGroups = isset($data['customerGroups']) ? $data['customerGroups'] : [];
        $this->_saveCustomerGroups($entity);

        //Lưu danh sách công thức
        $entity->formulas = isset($data['formulas']) ? $data['formulas'] : [];
        $this->_saveFormulas($entity);

        //Lưu danh sách phí rớt điểm
        $entity->pointCharges = isset($data['pointCharges']) ? $data['pointCharges'] : [];
        $this->_savePointCharges($entity);
    }

    public function _prepareForm()
    {
        //Get group vehicle list
        $vehicleGroupList = VehicleGroup::getScopedNestedList('name', 'id', '-', true);

        $this->setViewData([
            'customerGroupList' => $this->getCustomerGroupRepository()->getListForSelect(),
            'vehicleGroupList' => $vehicleGroupList,
            'locationGroupList' => $this->getLocationGroupRepository()->getListForSelect(),
            'goodsTypeList' => $this->getGoodsTypeRepository()->getListForSelect(),
        ]);
    }

    protected function _prepareFormWithID($id)
    {
        $attributes = $this->_getFormData(false);
        $code = null;
        if (array_key_exists('code', $attributes)) {
            $code = $attributes['code'];
        } else {
            if ($id == -1) {
                $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_price_quote'));
            }
        }

        if (isset($attributes['customerGroups'])) {
            $currentListCustomerGroup = $attributes['customerGroups'];
        } else {
            $entity = $this->getRepository()->getItemById($id);
            $currentListCustomerGroup = isset($entity->customerGroups) ? $entity->customerGroups->pluck('id')->toArray() : [];
        }

        $this->setViewData([
            'code' => $code,
            'currentListCustomerGroup' => $currentListCustomerGroup
        ]);
    }

    protected function _prepareConfirm()
    {
        $entity = $this->_getFormData();

        $entity->formulas = $this->_prepareDataFormulas($entity);
        $entity->pointCharges = $this->_prepareDataPointCharges($entity);

        $this->setEntity($entity);

        $this->setViewData([
            'show_history' => false,
        ]);
        $vehicleGroupList = VehicleGroup::getScopedNestedList('name', 'id', '-', false);
        $this->setViewData([
            'customerGroupList' => $this->getCustomerGroupRepository()->getListForSelect(),
            'vehicleGroupList' => $vehicleGroupList,
            'locationGroupList' => $this->getLocationGroupRepository()->getListForSelect(),
            'goodsTypeList' => $this->getGoodsTypeRepository()->getListForSelect(),
        ]);
    }

    protected function _prepareShow($id)
    {
        $entity = $this->getRepository()->findWithRelation($id);

        $entity->customerGroups = isset($entity->customerGroups) ? $entity->customerGroups->pluck('id')->toArray() : [];
        $entity->formulas = isset($entity->formulas) ? $entity->formulas->toArray() : [];
        $entity->pointCharges = isset($entity->pointCharges) ? $entity->pointCharges->toArray() : [];

        $this->setEntity($entity);

        $this->setViewData([
            'show_history' => true
        ]);
        $vehicleGroupList = VehicleGroup::getScopedNestedList('name', 'id', '-', false);
        $this->setViewData([
            'customerGroupList' => $this->getCustomerGroupRepository()->getListForSelect(),
            'vehicleGroupList' => $vehicleGroupList,
            'locationGroupList' => $this->getLocationGroupRepository()->getListForSelect(),
            'goodsTypeList' => $this->getGoodsTypeRepository()->getListForSelect(),
        ]);
    }

    protected function _prepareEdit($id = null)
    {
        $parent = parent::_prepareEdit($id);
        $entity = $this->getEntity();
        $entity->formulas = $this->_prepareDataFormulas($entity);
        $entity->pointCharges = $this->_prepareDataPointCharges($entity);

        $this->setEntity($entity);
        return $parent;
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
        $entity->date_from = empty($entity->date_from) ? null : AppConstant::convertDate($entity->date_from, 'Y-m-d');
        $entity->date_to = empty($entity->date_to) ? null : AppConstant::convertDate($entity->date_to, 'Y-m-d');
        $entity->isApplyAll = empty($entity->isApplyAll) ? 0 : $entity->isApplyAll;
        $entity->isDefault = empty($entity->isDefault) ? 0 : $entity->isDefault;
        $entity->isDistance = empty($entity->isDistance) ? 0 : $entity->isDistance;
        return $entity;
    }

    /**
     * Lưu nhóm khách hàng
     * @param $entity
     */
    protected function _saveCustomerGroups($entity)
    {
        if ($entity->isApplyAll == 1) {
            $this->getPriceQuoteCustomerGroupRepository()->deleteWhere([
                'price_quote_id' => $entity->id
            ]);
        } else {
            $customerGroups = $entity->customerGroups;

            if (empty($customerGroups)) {
                return;
            }
            $data = [];
            foreach ($customerGroups as $customerGroupId) {
                if (!empty($entity->id) && !empty($customerGroupId))
                    $data[] = [
                        'price_quote_id' => $entity->id,
                        'customer_group_id' => $customerGroupId
                    ];
            }
            $entity->customerGroups()->sync($data);
        }
    }

    /**
     * Lưu danh sách công thức
     * @param $entity
     */
    protected function _saveFormulas($entity)
    {
        $data = $this->_prepareDataFormulas($entity);

        $formulaModels = [];
        foreach ($data as $item) {
            $formulaModels[] = new PriceQuoteFormula($item);
        }
        $entity->formulas()->delete();
        $entity->formulas()->saveMany($formulaModels);
    }

    public function _prepareDataFormulas($entity)
    {
        $formulas = $entity->formulas;

        if (empty($formulas)) {
            return [];
        }
        $data = [];
        foreach ($formulas as $item) {
            if ($entity->type == config('constant.PRICE_QUOTE_VEHICLE_GROUP')) {
                $data[] = [
                    'price_quote_id' => $entity->id,
                    'vehicle_group_id' => isset($item['vehicle_group_id']) ? $item['vehicle_group_id'] : null,
                    'location_group_destination_id' => isset($item['location_group_destination_id']) ? $item['location_group_destination_id'] : null,
                    'location_group_arrival_id' => isset($item['location_group_arrival_id']) ? $item['location_group_arrival_id'] : null,
                    'price' => $item['price'],
                    'operator' => isset($item['operator']) ? $item['operator'] : 'equal',
                ];
            } elseif ($entity->type == config('constant.PRICE_QUOTE_WEIGHT')) {
                $data[] = [
                    'price_quote_id' => $entity->id,
                    'location_group_destination_id' => isset($item['location_group_destination_id']) ? $item['location_group_destination_id'] : null,
                    'location_group_arrival_id' => isset($item['location_group_arrival_id']) ? $item['location_group_arrival_id'] : null,
                    'operator' => isset($item['operator']) ? $item['operator'] : 'equal',
                    'weight_from' => $item['weight_from'],
                    'weight_to' => !empty($item['weight_to']) ? $item['weight_to'] : 0,
                    'price' => $item['price']
                ];
            } elseif ($entity->type == config('constant.PRICE_QUOTE_VOLUME')) {
                $data[] = [
                    'price_quote_id' => $entity->id,
                    'location_group_destination_id' => isset($item['location_group_destination_id']) ? $item['location_group_destination_id'] : null,
                    'location_group_arrival_id' => isset($item['location_group_arrival_id']) ? $item['location_group_arrival_id'] : null,
                    'operator' => isset($item['operator']) ? $item['operator'] : 'equal',
                    'volume_from' => $item['volume_from'],
                    'volume_to' => !empty($item['volume_to']) ? $item['volume_to'] : 0,
                    'price' => $item['price']
                ];
            } elseif ($entity->type == config('constant.PRICE_QUOTE_QUANTITY')) {
                $data[] = [
                    'price_quote_id' => $entity->id,
                    'goods_type_id' => isset($item['goods_type_id']) ? $item['goods_type_id'] : null,
                    'location_group_destination_id' => isset($item['location_group_destination_id']) ? $item['location_group_destination_id'] : null,
                    'location_group_arrival_id' => isset($item['location_group_arrival_id']) ? $item['location_group_arrival_id'] : null,
                    'price' => $item['price'],
                    'operator' => isset($item['operator']) ? $item['operator'] : 'equal',
                ];
            }
        }

        return $data;
    }

    /**
     * Lưu danh sách phí rớt điểm
     * @param $entity
     */
    protected function _savePointCharges($entity)
    {

        $data = $this->_prepareDataPointCharges($entity);
        $pointChargesModels = [];
        foreach ($data as $item) {
            $pointChargesModels[] = new PriceQuotePointCharge($item);
        }
        $entity->pointCharges()->delete();
        $entity->pointCharges()->saveMany($pointChargesModels);
    }

    public function _prepareDataPointCharges($entity)
    {
        $pointCharges = $entity->pointCharges;

        if (empty($pointCharges)) {
            return [];
        }
        $data = [];
        foreach ($pointCharges as $item) {
            if ($entity->type == config('constant.PRICE_QUOTE_VEHICLE_GROUP')) {
                $data[] = [
                    'price_quote_id' => $entity->id,
                    'vehicle_group_id' => isset($item['vehicle_group_id']) ? $item['vehicle_group_id'] : null,
                    'price' => $item['price'],
                    'operator' => isset($item['operator']) ? $item['operator'] : 'equal',
                ];
            } elseif ($entity->type == config('constant.PRICE_QUOTE_WEIGHT')) {
                $data[] = [
                    'price_quote_id' => $entity->id,
                    'operator' => isset($item['operator']) ? $item['operator'] : 'equal',
                    'weight_from' => $item['weight_from'],
                    'weight_to' => !empty($item['weight_to']) ? $item['weight_to'] : 0,
                    'price' => $item['price']
                ];
            } elseif ($entity->type == config('constant.PRICE_QUOTE_VOLUME')) {
                $data[] = [
                    'price_quote_id' => $entity->id,
                    'operator' => isset($item['operator']) ? $item['operator'] : 'equal',
                    'volume_from' => $item['volume_from'],
                    'volume_to' => !empty($item['volume_to']) ? $item['volume_to'] : 0,
                    'price' => $item['price']
                ];
            } elseif ($entity->type == config('constant.PRICE_QUOTE_QUANTITY')) {
                $data[] = [
                    'price_quote_id' => $entity->id,
                    'goods_type_id' => isset($item['goods_type_id']) ? $item['goods_type_id'] : null,
                    'price' => $item['price'],
                    'operator' => isset($item['operator']) ? $item['operator'] : 'equal',
                ];
            }
        }
        return $data;
    }

    /**
     * Lấy dữ liệu select2
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataForComboBox(Request $request)
    {

        $query = $this->getRepository()->getPriceQuotes($request);
        return response()->json(
            [
                'items' => $query->toArray()['data'],
                'pagination' => $query->nextPageUrl() ? true : false
            ]
        );
    }

    /**
     * Tự động tính giá trong đơn hàng
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoPrice(Request $request)
    {
        ini_set('max_execution_time', 8000000);
        try {
            $validation = Validator::make($request->all(), [
                'from_date' => 'required',
                'to_date' => 'required',
                'day_condition' => 'required',
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'errorCode' => HttpCode::EC_BAD_REQUEST,
                    'errorMessage' => $validation->messages()
                ]);
            } else {
                $orders = $this->getOrderRepository()->getOrdersToCalcPrice($request);

                $priceQuoteMemo = [];
                foreach ($orders as $order) {

                    $order_id = $order->order_id;
                    if (!array_key_exists($order->customer_id, $priceQuoteMemo)) {
                        $param = [
                            'customerId' => $order->customer_id
                        ];
                        $priceQuotes = $this->getRepository()->getPriceQuotes($param);
                        $priceQuoteMemo[$order->customer_id] = $priceQuotes;
                    } else {
                        $priceQuotes = $priceQuoteMemo[$order->customer_id];
                    }

                    if ($priceQuotes) {
                        $detail = $this->getOrderRepository()->getOrderDetailToCalcPrice($order_id);
                        $detail_with_goods = $this->getOrderRepository()->getOrderDetailToCalcPrice($order_id,   true);
                        foreach ($priceQuotes as $priceQuote) {
                            $type = $priceQuote->type;
                            $results = [];
                            switch ($type) {
                                case 1:
                                    $results = $this->getRoutesRepository()->_calcPriceByVehicle($priceQuote, [$detail], 'vehicle_group_id', false, 'equal');
                                    break;
                                case 2:
                                    $results = $this->getRoutesRepository()->_calcPriceByVehicle($priceQuote, [$detail], 'weight', false);
                                    break;
                                case 3:
                                    $results = $this->getRoutesRepository()->_calcPriceByVehicle($priceQuote, [$detail], 'volume', false);
                                    break;
                                case 4:
                                    $results =  $this->getRoutesRepository()->_calcPriceByVehicle($priceQuote, [$detail_with_goods], 'goods.goods_type_id', true, 'equal');
                                    break;
                            }
                            if (count($results) > 0) {
                                $result = $results[0];
                                if ($result['amount']  != null && $result['amount'] != 0) {
                                    $entity = $this->getOrderPriceRepository()->findFirstOrNew([]);
                                    $entity->order_id = $order_id;
                                    $entity->price_quote_id = $priceQuote->id;
                                    $entity->amount = $result['amount'];
                                    $entity->description = $result['description'];
                                    $entity->save();
                                    break;
                                }
                            }
                        }
                    }
                }

                return response()->json([
                    'errorCode' => HttpCode::EC_OK,
                    'errorMessage' => '',
                ]);
            }
        } catch (Exception $exception) {
            logError($exception);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => HttpCode::getMessageForCode(HttpCode::EC_APPLICATION_ERROR)
            ]);
        }
    }
}
