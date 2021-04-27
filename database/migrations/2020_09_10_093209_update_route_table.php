<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRouteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        SET SQL_SAFE_UPDATES = 0;

        UPDATE 
        routes i,
        (SELECT ro.route_id, 
            group_concat(o.order_code ORDER BY o.order_code SEPARATOR ';') AS order_codes,
            group_concat(o.vin_no ORDER BY o.order_code SEPARATOR ';') AS vin_nos,
            group_concat(o.model_no ORDER BY o.order_code SEPARATOR ';') AS model_nos,
            group_concat(distinct o.customer_id ORDER BY o.order_code SEPARATOR ',') AS customer_ids,
            SUM(distinct (o.amount - coalesce(o.commission_amount, 0 ) - coalesce(op.anonymous_amount, 0))) as total_amount,
            count(o.id) AS count_order,
            SUM(o.quantity) AS quantity,
            SUM(o.volume) AS volume,
            SUM(o.weight) AS weight
             from route_order  ro 
             left join orders o on o.id = ro.order_id  and o.del_flag = 0
             left join order_payment op on op.order_id = o.id and op.del_flag = 0
             where ro.del_flag = 0
             GROUP BY ro.route_id) AS route_lookup
        SET i.order_codes = route_lookup.order_codes,
        i.customer_ids = route_lookup.customer_ids,
        i.count_order = route_lookup.count_order,
        i.volume = route_lookup.volume,
        i.weight = route_lookup.weight,
        i.quantity = route_lookup.quantity,
        i.vin_nos = route_lookup.vin_nos,
        i.model_nos = route_lookup.model_nos,
        i.total_amount = coalesce(route_lookup.total_amount, 0)
        WHERE route_lookup.route_id = i.id;
        
        "
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
