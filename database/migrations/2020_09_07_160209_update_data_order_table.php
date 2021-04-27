<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDataOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('UPDATE orders a 
        JOIN order_trip b ON a.id = b.order_id
        join driver_trip c on c.trip_id = b.trip_id
        SET a.vehicle_id = c.vehicle_id,
        a.primary_driver_id = c.driver_id
        where c.del_flag = 0
        and b.del_flag = 0
        and c.driver_type = 1;
        
        UPDATE orders a 
            JOIN order_trip b ON a.id = b.order_id
            join driver_trip c on c.trip_id = b.trip_id
        SET a.secondary_driver_id = c.driver_id
        where c.del_flag = 0
        and b.del_flag = 0
        and c.driver_type = 2;
        
       /* DROP TABLE IF EXISTS `order_trip`;
        DROP TABLE IF EXISTS `driver_trip`;
        DROP TABLE IF EXISTS `trip`;*/

        '
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
