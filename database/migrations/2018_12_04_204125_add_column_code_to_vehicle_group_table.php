<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnCodeToVehicleGroupTable extends Base
{
    protected $_table = 'm_vehicle_group';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('code', 50)->after('id')->nullable();
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
            $table->dropColumn('code');
        });
    }
}
