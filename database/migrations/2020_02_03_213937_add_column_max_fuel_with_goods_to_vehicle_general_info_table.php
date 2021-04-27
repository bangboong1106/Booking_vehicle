<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnMaxFuelWithGoodsToVehicleGeneralInfoTable extends Base
{
    protected $_table = 'vehicle_general_info';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->decimal('max_fuel_with_goods', 18, 4)->nullable()->after('max_fuel');
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
            $table->dropColumn('max_fuel_with_goods');
        });
    }
}
