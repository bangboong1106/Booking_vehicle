<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;
use App\Model\Entities\Order;
use Illuminate\Support\Facades\DB;

class TransferOrderToOrderLocations extends Base
{
    protected $_table = 'order_locations';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $orders = Order::where(getDelFlagColumn(), '=', '0')->get();
        $data = [];
        foreach ($orders as $order) {
            if (!empty($order->location_destination_id)) {
                $data[] = [
                    'order_id' => $order->id,
                    'location_id' => $order->location_destination_id,
                    'type' => config('constant.DESTINATION'),
                    'date' => $order->ETD_date,
                    'date_reality' => $order->ETD_date_reality,
                    'time' => $order->ETD_time,
                    'time_reality' => $order->ETD_time_reality,
                    'note' => '',
                ];
            }

            if (!empty($order->location_arrival_id)) {
                $data[] = [
                    'order_id' => $order->id,
                    'location_id' => $order->location_arrival_id,
                    'type' => config('constant.ARRIVAL'),
                    'date' => $order->ETA_date,
                    'date_reality' => $order->ETA_date_reality,
                    'time' => $order->ETA_time,
                    'time_reality' => $order->ETA_time_reality,
                    'note' => '',
                ];
            }
        }
        DB::table($this->getTable())->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table($this->getTable())->truncate();
    }
}
