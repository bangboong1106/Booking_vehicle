<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateDriverTripTable extends \App\Database\Migration\Create
{
    protected $_table = 'driver_trip';

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
            $table->integer('trip_id')->nullable();

            $table->string('register_no', 100)->nullable();
            $table->string('mobile_no', 100)->nullable();
            $table->string('driver_name')->nullable();
            $table->string('reason')->nullable();
            $table->string('note')->nullable();
            $table->string('status')->nullable();

            $table->date('ETD_date')->nullable();
            $table->time('ETD_time')->nullable();
            $table->date('ETA_date')->nullable();
            $table->time('ETA_time')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
