<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddDriverAndVehicleToOrderTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'vehicle_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('vehicle_id')->nullable()->after('status');
                $table->index('vehicle_id');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'primary_driver_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('primary_driver_id')->nullable()->after('status');
                $table->index('primary_driver_id');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'secondary_driver_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('secondary_driver_id')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('vehicle_id');
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('primary_driver_id');
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('secondary_driver_id');
        });
    }
}
