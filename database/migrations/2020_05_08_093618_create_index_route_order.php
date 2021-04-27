<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateIndexRouteOrder extends Base
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_order', function (Blueprint $table) {
            $table->index(['route_id']);
            $table->index(['order_id']);
        });

    }

    public function down()
    {
        Schema::table('route_order', function (Blueprint $table) {
            $table->dropIndex(['route_id']);
            $table->dropIndex(['order_id']);
        });
    }
}
