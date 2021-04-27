<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnETAETDRealityToOrderTable extends Base
{
    protected $_table = 'orders';

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

            $table->date('ETD_date_reality')->nullable()->after('ETD_time');
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
            $table->dropColumn('ETA_date_reality');
            $table->dropColumn('ETA_time_reality');
            $table->dropColumn('ETD_date_reality');
            $table->dropColumn('ETD_time_reality');
        });
    }
}
