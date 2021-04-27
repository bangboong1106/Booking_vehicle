<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyReportOperatingDailyTable extends Base
{
    protected $_table = 'report_operating_daily';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->decimal('amount_ETD', 18, 4)->nullable()->after('value');
            $table->decimal('amount_ETD_reality', 18, 4)->nullable()->after('amount_ETD');
            $table->decimal('amount_ETA', 18, 4)->nullable()->after('amount_ETD_reality');
            $table->decimal('amount_ETA_reality', 18, 4)->nullable()->after('amount_ETA');
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('amount_ETD');
            $table->dropColumn('amount_ETD_reality');
            $table->dropColumn('amount_ETA');
            $table->dropColumn('amount_ETA_reality');
        });
    }
}
