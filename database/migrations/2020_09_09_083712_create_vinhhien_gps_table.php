<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVinhHienGPSTable extends \App\Database\Migration\Create
{
    protected $_table = 'vinhhien_gps';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->string('vehicle_plate')->nullable();
            $table->timestamp('datetime')->nullable();
            $table->char('lat', 10)->nullable();
            $table->char('lon', 10)->nullable();
            $table->string('address')->nullable();
            $table->integer('current_total_km')->nullable()->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('vinhhien_gps');
    }
}
