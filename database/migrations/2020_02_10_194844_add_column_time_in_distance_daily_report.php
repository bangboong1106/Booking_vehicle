<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnTimeInDistanceDailyReport extends Base
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
            $table->dateTime('from_time')->nullable()->after('distance_with_goods');
            $table->dateTime('to_time')->nullable()->after('from_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('from_time');
            $table->dropColumn('to_time');
        });
    }
}
