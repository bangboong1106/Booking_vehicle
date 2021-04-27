<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyColumnDistanceDailyReportTable extends Base
{
    protected $_table = 'distance_daily_report';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('distance')->nullable()->change();
            $table->integer('distance_with_goods')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('distance')->nullable()->change();
            $table->integer('distance_with_goods')->nullable()->change();
        });
    }
}
