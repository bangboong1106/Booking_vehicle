<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddAmountEstimateToOrderCustomerTable extends Base
{
    protected $_table = 'order_customer';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'amount_estimate')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->decimal('amount_estimate', 18, 4)->nullable()->after('amount');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'ETA_date_desired')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->date('ETA_date_desired')->nullable()->after('ETA_date');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'ETA_time_desired')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->time('ETA_time_desired')->nullable()->after('ETA_time');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn($this->getTable(), 'amount_estimate')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('amount_estimate');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'ETA_date_desired')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('ETA_date_desired');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'ETA_time_desired')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('ETA_time_desired');
            });
        }
    }
}
