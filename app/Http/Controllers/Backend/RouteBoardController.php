<?php

namespace App\Http\Controllers\Backend;

use App\Common\HttpCode;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * Class RouteBoardController
 * @package App\Http\Controllers\Backend
 */
class RouteBoardController extends BaseBoardController
{
    protected $_action_list = 'timelineDay,customTimelineWeek,customTwoWeekDate,customTimelineMonth,customDate,hiddenDate refreshButton, fullscreenButton';

    protected $__isEventResize = false;
    protected $_isDrag = true;
    protected $_isRaw = false;

    protected function getModel(): string
    {
        return 'route_board';
    }

    protected function getStatusList()
    {
        return [
            config("constant.status_incomplete"),
            config("constant.status_complete"),
            config("constant.status_cancel"),
        ];
    }

    protected function getOrderListFunction()
    {
        return 'getListForRouteBoard';
    }

    protected function getRepositoryFunction()
    {
        return 'getMergeOrderRepository';
    }

    // Lấy danh sách xe hiển thị trên calendar
    // CreatedBy nlhoang 18/08/2020
    public function vehicleList()
    {
        return $this->scheduler();
    }

    // Lấy danh sách xe hiển thị trên calendar
    // CreatedBy nlhoang 18/08/2020
    public function vehicleDetail()
    {
        return $this->vehicle();
    }

    // Lấy danh sách chuyến xe hiển thị trên calendar
    // CreatedBy nlhoang 18/08/2020
    public function routeList()
    {
        return $this->event();
    }

    // Lấy danh sách đơn hàng được ghép chuyến hiển thị trên calendar
    // CreatedBy nlhoang 18/08/2020
    public function orderList()
    {
        return parent::orderList();
    }

    protected function buildQuery($params)
    {
        return $this->getRouteRepository()->buildQueryForBoard($params);
    }

    // Trả lại màn hình chọn chuyến
    // CreatedBy nlhoang 19/08/2020
    public function chooseRoute()
    {
        $vehicleId = Request::get('vehicle_id');
        $start = Request::get('start');
        $end = Request::get('end');

        $params = [
            'vehicle_id' => $vehicleId,
            'start' => $start,
            'end' => $end
        ];
        $routes = $this->getRouteRepository()->getRouteListByVehicle($params);
        $default_driver = $this->getDriverVehicleRepository()->getItemByVehicleID($vehicleId);
        $drivers = $this->getDriverRepository()->all(["id", 'full_name'])->sortBy('full_name');

        $this->setViewData([
            'routes' => $routes,
            'default_driver' => $default_driver,
            'drivers' => $drivers,
        ]);


        $html = [
            'content' => $this->render('backend.route_board._choose_route')->render(),
        ];

        $this->setData($html);

        return $this->renderJson();
    }

    // Thực hiện ghép chuyến
    // CreatedBy nlhoang 19/08/2020
    public function mergeRoute()
    {
        try {
            DB::beginTransaction();

            $route_id = request()->get('route_id');
            $vehicle_id = request()->get('vehicle_id');
            $driver_id = request()->get('driver_id');
            $order_id = request()->get('order_id');

            $message = app('App\Http\Controllers\Backend\MergeOrderController')->addOrderToRoute([$order_id], $route_id, $vehicle_id, $driver_id);

            DB::commit();

            return response()->json([
                'errorCode' => empty($message) ? HttpCode::EC_OK : HttpCode::EC_APPLICATION_WARNING,
                'errorMessage' => $message,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            logError($e);
            return response()->json([
                'errorCode' => HttpCode::EC_APPLICATION_ERROR,
                'errorMessage' => $e->getMessage()
            ]);
        }
    }
}
