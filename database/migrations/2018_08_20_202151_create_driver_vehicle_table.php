<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateDriverVehicleTable extends \App\Database\Migration\Create
{
    protected $_table = 'driver_vehicle';

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

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->integer('vehicle_id')->nullable();
            $table->integer('driver_id')->nullable();
            $table->string('note')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
