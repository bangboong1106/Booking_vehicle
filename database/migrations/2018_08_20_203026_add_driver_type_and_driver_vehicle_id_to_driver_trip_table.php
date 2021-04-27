<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddDriverTypeAndDriverVehicleIdToDriverTripTable extends Base
{
    protected $_table = 'driver_trip';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('driver_vehicle_id')->nullable()->after('driver_id');
            $table->char('driver_type')->nullable()->after('driver_vehicle_id');

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
            $table->dropColumn('driver_vehicle_id');
            $table->dropColumn('driver_type');
        });
    }
}
