<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddCapacityToRouteTable extends Base
{
    protected $_table = 'routes';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('capacity_weight_ratio')->nullable()->after('route_note');
            $table->integer('capacity_volume_ratio')->nullable()->after('capacity_weight_ratio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('capacity_weight_ratio');
            $table->dropColumn('capacity_volume_ratio');
        });
    }
}
