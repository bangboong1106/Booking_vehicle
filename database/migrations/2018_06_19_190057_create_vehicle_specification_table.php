<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVehicleSpecificationTable extends \App\Database\Migration\Create
{
    protected $_table = 'vehicle_specification';

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
            $table->integer('vehicle_id');
            $table->integer('vehicle_config_specification_id');
            $table->char('value', 255);
            $table->char('unit', 1)->nullable();
            
            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_specification');
    }
}
