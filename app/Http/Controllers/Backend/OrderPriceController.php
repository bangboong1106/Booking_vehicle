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

class OrderPriceController extends BackendController
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

    public function __construct(
        OrderPriceRepository $orderPriceRepository,
        OrderRepository $orderRepository,
        PriceQuoteCustomerGroupRepository $priceQuoteCustomerGroupRepository,
        CustomerGroupRepository $customerGroupRepository,
        LocationGroupRepository $locationGroupRepository,
        GoodsTypeRepository $GoodsTypeRepository,
        ColumnConfigRepository $columnConfigRepository

    ) {
        parent::__construct();

        $this->setRepository($orderPriceRepository);
        $this->setPriceQuoteCustomerGroupRepository($priceQuoteCustomerGroupRepository);
        $this->setCustomerGroupRepository($customerGroupRepository);
        $this->setLocationGroupRepository($locationGroupRepository);
        $this->setGoodsTypeRepository($GoodsTypeRepository);
        $this->setColumnConfigRepository($columnConfigRepository);
        $this->setOrderRepository($orderRepository);

        $this->setBackUrlDefault('order-price.index');
        $this->setMenu('quota');
        $this->setTitle(trans('models.order_price.name'));
    }

    protected function _prepareIndex()
    {
        $userId = Auth::User()->id;
        $configList = $this->getColumnConfigRepository()->getConfigList($userId, config('constant.cf_order_price'));
        $this->setViewData([
            'configList' => $configList["configList"],
            'sort_field' => $configList["sort_field"],
            'sort_type' => $configList["sort_type"],
            'page_size' => $configList["page_size"]
        ]);
    }


    // Cập nhật giá cho đơn hàng
    // CreatedBy nlhoang 04/08/2020
    protected function price(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
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

            $items = $this->getRepository()->getOrderPricesByIds(explode(',', $data));
            DB::beginTransaction();
            try {
                foreach ($items as $item) {
                    $order = $this->getOrderRepository()->getItemById($item->order_id);
                    $order->amount =  $item->amount;
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

                    $item->is_approved = 1;
                    $item->approved_id = Auth::User()->id;
                    $item->approved_date = now();
                    $item->save();
                }
                DB::commit();
            } catch (Exception $e) {

                DB::rollBack();
                return response()->json([
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'errorCode' => HttpCode::EC_OK,
                'errorMessage' => '',
            ]);
        } catch (Exception $exception) {
            logError($exception);
            return response()->json(
                [
                    'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                    'errorMessage' => $exception->getMessage()
                ]
            );
        }
    }
}
