<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateDemoReportDataTable extends \App\Database\Migration\Create
{
    protected $_table = 'demo_report_data';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->string('report_id')->nullable();
            $table->string('report_name')->nullable();
            $table->String('vehicle')->nullable();
            $table->String('client')->nullable();
            $table->String('driver')->nullable();
            $table->float('status_all')->nullable();
            $table->float('status_complete')->nullable();
            $table->float('status_incomplete')->nullable();
            $table->float('status_on_time')->nullable();
            $table->float('status_late')->nullable();
            $table->float('status_future')->nullable();
            $table->date('date')->nullable();
            $table->string('month')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('demo_report_data');
    }
}
