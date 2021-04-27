<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateDistanceDailyReport extends \App\Database\Migration\Create
{
    protected $_table = 'distance_daily_report';

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

            $table->date('date')->nullable();
            $table->integer('route_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->string('reg_no', 20)->nullable();
            $table->string('vehicle_plate', 20)->nullable();
            $table->string('gps_id', 20)->nullable();

            $table->integer('distance');
            $table->integer('distance_with_goods');

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
        Schema::dropIfExists('vehicle_distance_daily_report');
    }
}
