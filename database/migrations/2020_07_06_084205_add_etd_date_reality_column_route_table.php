<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddEtdDateRealityColumnRouteTable extends Base
{
    protected $_table = 'routes';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->date('ETD_date_reality')->nullable()->after('ETD_date');
            $table->time('ETD_time_reality')->nullable()->after('ETD_date_reality');
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
            $table->dropColumn('ETD_date_reality');
            $table->dropColumn('ETD_time_reality');
        });
    }
}
