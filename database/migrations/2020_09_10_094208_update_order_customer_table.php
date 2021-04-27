<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderCustomerTable extends Migration
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
         order_customer i,
         (SELECT ro.order_customer_id, 
             group_concat(o.order_code ORDER BY o.order_code SEPARATOR ';') AS order_codes,
             group_concat(o.vin_no ORDER BY o.order_code SEPARATOR ';') AS vin_nos,
             group_concat(o.model_no ORDER BY o.order_code SEPARATOR ';') AS model_nos,
             count(o.id) AS count_order
              from order_customer_order  ro 
              left join orders o on o.id = ro.order_id  and o.del_flag = 0
              where ro.del_flag = 0
              GROUP BY ro.order_customer_id) AS route_lookup
         SET i.order_codes = route_lookup.order_codes,
         i.count_order = route_lookup.count_order,
         i.vin_nos = route_lookup.vin_nos,
         i.model_nos = route_lookup.model_nos
         WHERE route_lookup.order_customer_id = i.id;
        
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
