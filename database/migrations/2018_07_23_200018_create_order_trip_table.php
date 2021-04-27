<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateOrderTripTable extends \App\Database\Migration\Create
{
    protected $_table = 'order_trip';

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

            $table->string('name')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('order_no', 100)->nullable();
            $table->integer('trip_id')->nullable();
            $table->string('status', 30)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
