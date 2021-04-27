<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ChangeDriverVehicleTeamTable extends Base
{
    protected $_table = 'driver_vehicle_team';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('id');

            $table->primary(['driver_id', 'vehicle_team_id'],
                'driver_vehicle_team_primary');
        });
        Schema::drop('role');
        Schema::table('vehicle_team', function (Blueprint $table) {
            $table->dropColumn('driver_ids');
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
            $table->integer('id', true);
            $table->dropPrimary('driver_vehicle_team_primary');
        });
        Schema::table('vehicle_team', function (Blueprint $table) {
            $table->string('driver_ids', 1000);
        });
    }
}
