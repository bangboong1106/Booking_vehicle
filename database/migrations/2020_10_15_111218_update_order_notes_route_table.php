<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderNotesRouteTable extends Migration
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

        UPDATE routes r
            LEFT JOIN (SELECT o.route_id, group_concat(distinct o.note SEPARATOR '|') note FROM orders o
                        WHERE o.del_flag = 0 AND o.note IS NOT NULL AND o.note != ''
                        GROUP BY o.route_id
                        ) tmp ON tmp.route_id = r.id
        SET r.order_notes = tmp.note;
        ");
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
