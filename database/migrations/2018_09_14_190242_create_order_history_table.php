<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateOrderHistoryTable extends \App\Database\Migration\Create
{
    protected $_table = 'order_history';

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

            $table->integer('order_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('primary_driver_id')->nullable();
            $table->integer('secondary_driver_id')->nullable();
            $table->integer('trip_id')->nullable();
            $table->integer('order_status')->nullable();
            $table->char('latitude', 10)->nullable();
            $table->char('longitude', 10)->nullable();
            $table->char('current_location', 255)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
