<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTripTable extends \App\Database\Migration\Create
{
    protected $_table = 'trip';

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

            $table->string('name');
            $table->string('trip_no');
            $table->text('status');

            $table->date('ETD_date')->nullable();
            $table->time('ETD_time')->nullable();
            $table->integer('location_destination_id')->nullable();

            $table->date('ETA_date')->nullable();
            $table->time('ETA_time')->nullable();
            $table->integer('location_arrival_id')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
