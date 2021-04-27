<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ChangeEtaEtdTimeOrderTable extends Base
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
            $table->time('ETA_time')->nullable()->after('ETA_date')->change();
            $table->time('ETD_time')->nullable()->after('ETD_date')->change();
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
            $table->date('ETA_time')->nullable()->after('ETA_date')->change();
            $table->date('ETD_time')->nullable()->after('ETD_date')->change();
        });
    }
}
