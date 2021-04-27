<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateDriverVehicleTeamTable extends \App\Database\Migration\Create
{
    protected $_table = 'driver_vehicle_team';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create('driver_vehicle_team', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('driver_id')->nullable();
            $table->integer('vehicle_team_id')->nullable();
        });
    }
}
