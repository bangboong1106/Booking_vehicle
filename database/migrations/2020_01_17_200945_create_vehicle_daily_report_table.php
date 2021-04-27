<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVehicleDailyReportTable extends \App\Database\Migration\Create
{
    protected $_table = 'vehicle_daily_report';

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

            $table->string('gps_id', 20)->nullable();
            $table->string('reg_no', 20)->nullable();
            $table->string('vehicle_plate', 20)->nullable();
            $table->date('date')->nullable();
            $table->integer('distance');
            $table->integer('door_open_count');
            $table->integer('over_speed_count');
            $table->integer('max_speed');
            $table->dateTime('first_acc_on_time')->nullable();
            $table->dateTime('last_acc_off_time')->nullable();
            $table->integer('acc_time');
            $table->integer('run_time');
            $table->integer('idle_time');
            $table->integer('stop_time');
            $table->dateTime('sys_gps_time')->nullable();
            $table->dateTime('date_gps_return')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
