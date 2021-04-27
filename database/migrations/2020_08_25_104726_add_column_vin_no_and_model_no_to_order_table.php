<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnVinNoAndModelNoToOrderTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'model_no')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('model_no', 100)->nullable()->after('order_no');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'vin_no')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('vin_no', 100)->nullable()->after('model_no');
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
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('model_no');
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('vin_no');
        });
    }
}
