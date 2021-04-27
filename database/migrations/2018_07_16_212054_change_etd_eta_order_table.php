<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ChangeEtdEtaOrderTable extends Base
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
            $table->date('ETA_date')->nullable()->after('ETA');
            $table->date('ETA_time')->nullable()->after('ETA_date');

            $table->date('ETD_date')->nullable()->after('ETD');
            $table->date('ETD_time')->nullable()->after('ETD_date');

            $table->dropColumn('ETA');
            $table->dropColumn('ETD');
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dateTime('ETA')->nullable()->after('contact_mobile_no_destination');
            $table->dateTime('ETD')->nullable()->after('customer_mobile_no');

            $table->dropColumn('ETA_date');
            $table->dropColumn('ETA_time');
            $table->dropColumn('ETD_date');
            $table->dropColumn('ETD_time');
        });
    }
}
