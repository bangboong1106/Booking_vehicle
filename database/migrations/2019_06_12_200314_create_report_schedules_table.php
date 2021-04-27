<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateReportSchedulesTable extends \App\Database\Migration\Create
{
    protected $_table = 'report_schedules';

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

            $table->string('description')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->integer('schedule_type')->nullable();
            $table->time('time_to_send')->nullable();


            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_schedules');
    }
}
