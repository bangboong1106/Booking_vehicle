<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVehicleGeneralInfoTable extends \App\Database\Migration\Create
{
    protected $_table = 'vehicle_general_info';

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
/*
        Schema::create($this->_table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vehicle_id');
            $table->decimal('gross_weight', 10, 4)->nullable();
            $table->decimal('net_weight', 10, 4)->nullable();
            $table->decimal('length', 10, 4)->nullable();
            $table->decimal('height', 10, 4)->nullable();
            $table->decimal('width', 10, 4)->nullable();
            $table->integer('register_year')->nullable();
            $table->char('brand')->nullable();
            $table->date('last_register_date')->nullable();
            $table->date('expire_register_date')->nullable();
            $table->date('last_insurance_date')->nullable();
            $table->date('expire_insurance_date')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_general_info');
    }
}
