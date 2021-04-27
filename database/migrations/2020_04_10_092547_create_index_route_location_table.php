<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateIndexRouteLocationTable extends Base
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('route_location', function (Blueprint $table) {
            $table->index(['route_id']);
        });

    }

    public function down()
    {
        Schema::table('route_location', function (Blueprint $table) {
            $table->dropIndex(['route_id']);
        });
    }
}
