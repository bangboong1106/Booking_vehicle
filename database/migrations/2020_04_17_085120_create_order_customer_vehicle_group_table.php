<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class CreateOrderCustomerVehicleGroupTable extends Base
{
    protected $_table = 'order_customer_vehicle_group';

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
            $table->integer('order_customer_id')->nullable();
            $table->integer('vehicle_group_id')->nullable();
            $table->string('vehicle_number')->nullable();

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
        Schema::dropIfExists('order_customer_vehicle_group');
    }
}
