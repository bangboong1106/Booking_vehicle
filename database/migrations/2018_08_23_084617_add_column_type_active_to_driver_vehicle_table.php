<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnTypeActiveToDriverVehicleTable extends Base
{
    protected $_table = 'driver_vehicle';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->char('driver_type')->nullable()->after('driver_id');
            $table->boolean('active')->nullable()->after('driver_type');

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
            $table->dropColumn('driver_type');
            $table->dropColumn('active');
        });
    }
}
