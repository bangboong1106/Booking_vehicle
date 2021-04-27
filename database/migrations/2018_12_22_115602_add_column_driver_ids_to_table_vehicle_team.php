<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnDriverIdsToTableVehicleTeam extends Base
{
    protected $_table = 'vehicle_team';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('driver_ids', 1000)->nullable()->after('capital_driver_id');
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
            $table->dropColumn('driver_ids');
        });
    }
}
