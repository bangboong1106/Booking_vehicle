<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnTypeToAlertLogTable extends Base
{
    protected $_table = 'alert_logs';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('alert_type')->nullable()->after('content');
            $table->integer('user_id')->nullable()->after('alert_type');
            $table->integer('driver_id')->nullable()->after('user_id');
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
            $table->dropColumn('alert_type');
            $table->dropColumn('user_id');
            $table->dropColumn('driver_id');
        });
    }
}
