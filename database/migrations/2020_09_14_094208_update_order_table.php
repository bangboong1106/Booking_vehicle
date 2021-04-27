<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderTable extends Migration
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

        UPDATE orders a 
            JOIN route_order b ON a.id = b.order_id AND b.del_flag = 0
        SET a.route_id = b.route_id;

        UPDATE orders a 
            JOIN order_customer_order b ON a.id = b.order_id AND b.del_flag = 0
        SET a.order_customer_id = b.order_customer_id;
        
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
