<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\OrderHistory;
use App\Model\Entities\Vehicle;
use App\Repositories\Base\CustomRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderHistoryRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return OrderHistory::class;
    }

    public function validator()
    {
        return \App\Validators\OrderHistoryValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with('order')->with('vehicle')->with('driver');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getOrderHistoryWithID($id)
    {
        if (!$id)
            return null;
        return $this->findFirstOrNew(['id' => $id]);
    }

    public function getOrderHistoryWithOrderId($orderId)
    {
        if (!$orderId)
            return null;
        return $this->search([
            'order_id_eq' => $orderId,
            'order_status_gteq' => config("constant.SAN_SANG"),
            'sort_type' => 'asc',
            'sort_field' => 'id'
        ])->get();
    }

    public function processUpdateOrderHistory($orderEntity)
    {
        if ($orderEntity->id) {
            $orderHistories = $this->search([
                'order_id_eq' => $orderEntity->id
            ])->get();
            if ($orderHistories)
                foreach ($orderHistories as $orderHistory) {
                    $orderHistory->customer_id = $orderEntity->customer_id;
                    $orderHistory->vehicle_id = $orderEntity->vehicle_id;
                    $orderHistory->primary_driver_id = $orderEntity->primary_driver_id;
                    $orderHistory->secondary_driver_id = $orderEntity->secondary_driver_id;
                    $orderHistory->save();
                }
        }
    }

    public function processCreateOrderHistory($orderEntity)
    {
        $orderHistoryEntity = $this->findFirstOrNew([]);
        $orderHistoryEntity->order_id = $orderEntity->id;
        $orderHistoryEntity->order_status = $orderEntity->status;
        $orderHistoryEntity->customer_id = $orderEntity->customer_id;
        $orderHistoryEntity->vehicle_id = $orderEntity->vehicle_id;
        $orderHistoryEntity->primary_driver_id = $orderEntity->primary_driver_id;
        $orderHistoryEntity->secondary_driver_id = $orderEntity->secondary_driver_id;

        $insUserId = getCurrentUserId();
        $currentLocationVehicle = null;
        // Nếu tài xế cập nhật thông tin đơn thì lấy vị trí hiện tại của xe. Còn ko thì lấy theo vị trí của thông tin đơn hàng
        if (isset($insUserId)) {
            $user = AdminUserInfo::query()->find($insUserId);
            if (isset($user)) {
                if ('driver' == $user->role) {
                    $currentLocationVehicle = $this->getCurrentLocationVehicle($orderEntity->vehicle_id);
                }
            }
        }

        switch ($orderEntity->status) {
            case config("constant.KHOI_TAO"):
            case config("constant.SAN_SANG"):
                $orderHistoryEntity->current_location = $orderEntity->locationDestination != null ? $orderEntity->locationDestination->full_address : '';
                break;
            case config("constant.CHO_NHAN_HANG"):
                $orderHistoryEntity->current_location = $orderEntity->locationDestination != null ? $orderEntity->locationDestination->full_address : '';
                break;
            case config("constant.DANG_VAN_CHUYEN"):
                $orderHistoryEntity->current_location = $currentLocationVehicle != null ? $currentLocationVehicle : ($orderEntity->locationDestination != null ? $orderEntity->locationDestination->full_address : '');
                break;
            case config("constant.HOAN_THANH"):
                $orderHistoryEntity->current_location = $currentLocationVehicle != null ? $currentLocationVehicle : ($orderEntity->locationArrival != null ? $orderEntity->locationArrival->full_address : '');
                break;
            case config("constant.HUY"):
                $orderHistoryEntity->current_location = $currentLocationVehicle != null ? $currentLocationVehicle : '';
                break;
        }

        $orderHistoryEntity->save();
    }

    public function getCurrentLocationVehicle($vehicleId)
    {
        if (isset($vehicleId)) {
            $vehicle = Vehicle::query()->find($vehicleId);
            if (isset($vehicle)) {
                return $vehicle->current_location;
            }
        }
        return null;
    }

    public function getLastOrderHistoryWithOrderId($orderId)
    {
        if (!$orderId)
            return null;
        return $this->search([
            'order_id_eq' => $orderId,
            'sort_type' => 'desc',
            'sort_field' => 'id'
        ])->first();
    }

    public function getCompletedItemsByDriverID($data)
    {
        $start_date = Carbon::createFromFormat('d-m-Y', $data['start_date'])->format('Y-m-d');
        $end_date = Carbon::createFromFormat('d-m-Y H:i:s', $data['end_date'] . ' 23:59:59')->format('Y-m-d H:i:s');
        $query = DB::table('order_history')
            ->where('order_history.del_flag', '=', 0)
            ->where('order_history.primary_driver_id', '=', $data['driver_id'])
            ->where('order_history.ins_date', '>=', $start_date)
            ->where('order_history.ins_date', '<=', $end_date)
            ->where('order_history.order_status', '=', config("constant.HOAN_THANH"));

        if (!empty($data['vehicle_id'])) {
            $query->where('order_history.vehicle_id', '=', $data['vehicle_id']);
        }
        if (!empty($data['customer_id'])) {
            $query->where('order_history.customer_id', '=', $data['customer_id']);
        }
        return $query;
    }

    public function getCompletedItemsByVehicleID($data)
    {
        $start_date = Carbon::createFromFormat('d-m-Y', $data['start_date'])->format('Y-m-d');
        $end_date = Carbon::createFromFormat('d-m-Y H:i:s', $data['end_date'] . ' 23:59:59')->format('Y-m-d H:i:s');
        $query = DB::table('order_history')
            ->where('order_history.del_flag', '=', 0)
            ->where('order_history.vehicle_id', '=', $data['vehicle_id'])
            ->where('order_history.ins_date', '>=', $start_date)
            ->where('order_history.ins_date', '<=', $end_date)
            ->where('order_history.order_status', '=', config("constant.HOAN_THANH"));

        if (!empty($data['driver_id'])) {
            $query->where('order_history.primary_driver_id', '=', $data['driver_id']);
        }
        if (!empty($data['customer_id'])) {
            $query->where('order_history.customer_id', '=', $data['customer_id']);
        }
        return $query;
    }
}

