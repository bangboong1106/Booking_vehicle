<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class OrderCustomerBoardController extends BaseBoardController
{
    protected $_action_list = 'autoScrollButton, timelineDay,customTimelineWeek,customTwoWeekDate,customTimelineMonth,customDate,hiddenDate refreshButton, fullscreenButton';

    protected $__isEventResize = false;
    protected $_isDrag = false;

    protected function getModel(): string
    {
        return 'order_customer_board';
    }

    protected function getResourceColumn(): string
    {
        return 'Chủ hàng';
    }

    protected function getStatusList()
    {
        return [
            config("constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG"),
            config("constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN"),
            config("constant.ORDER_CUSTOMER_STATUS.HOAN_THANH"),
            config("constant.ORDER_CUSTOMER_STATUS.C20_HUY"),
        ];
    }

    protected function getCustomButtons()
    {
        $buttons = parent::getCustomButtons();
        $buttons['autoScrollButton'] =
            array(
                'text' => 'Bật tự động cuộn',
                'id' => 'calendarAutoScroll'
            );
        return $buttons;
    }

    // Lấy danh sách khách hàng
    // CreatedBy nlhoang 18/08/2020
    public function scheduler()
    {
        $page = 1;
        $limit = 20;

        if (
            !empty(Request::get('page')) &&
            trim(Request::get('page')) !== '' && is_numeric(trim(Request::get('page')))
        ) {
            $page = intval(trim(Request::get('page')));
        }

        if (
            !empty(Request::get('page_size')) &&
            trim(Request::get('page_size')) !== '' && is_numeric(trim(Request::get('page_size')))
        ) {
            $limit = intval(trim(Request::get('page_size')));
        }

        $customerIDs = Request::get('customerIDs');

        $query = DB::table('customer')
            ->where('customer.del_flag', '=', 0)
            ->whereNull('customer.parent_id');

        if (!empty($customerIDs)) {
            $query = $query->whereIn('customer.id', explode(',', $customerIDs));
        }

        $total = $query->count();

        $items = $query->distinct()->select([
            'full_name as title',
            'id',
            DB::raw('("' . route("customer.show", -1) . '") as url')
        ])->orderBy('full_name')->paginate($limit);
        $this->setEntities($items);
        $model = [
            'total' => $total,
            'items' => $items,
            'page' => $page,
            'paginator' => $this->render('backend.order_customer_board._customer_pagination')->render()
        ];
        return json_encode($model);
    }

    protected function buildQuery($params)
    {
        return $this->getOrderCustomerRepository()->buildQueryForBoard($params);
    }

    public function detail($id)
    {
        $entity  = $this->getOrderCustomerRepository()->getItemById($id);
        $orders = $this->getOrderCustomerRepository()->getOrdersByID($id);
        $this->setViewData([
            'entity' => $entity,
            'orders' => $orders,
        ]);

        $html = [
            'content' => $this->render('backend.order_customer_board._order_customer_detail')->render(),
        ];

        $this->setData($html);

        return $this->renderJson();
    }

    public function getResourceItems($ids)
    {
        return $this->getCustomerRepository()->getItemsByIds($ids);
    }
}
