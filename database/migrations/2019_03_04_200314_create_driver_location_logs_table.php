<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateDriverLocationLogsTable extends \App\Database\Migration\Create
{
    protected $_table = 'driver_location_logs';

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

            $table->string('d_uniqueID')->nullable();
            $table->text('request_data')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_location_logs');
    }
}
