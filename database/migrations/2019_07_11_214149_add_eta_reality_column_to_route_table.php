<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddEtaRealityColumnToRouteTable extends Base
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
            $table->date('ETA_date_reality')->nullable()->after('ETA_time');
            $table->time('ETA_time_reality')->nullable()->after('ETA_date_reality');
            $table->renameColumn('status', 'route_status');
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
            $table->dropColumn('ETA_date_reality');
            $table->dropColumn('ETA_time_reality');
            $table->renameColumn('route_status', 'status');
        });
    }
}
