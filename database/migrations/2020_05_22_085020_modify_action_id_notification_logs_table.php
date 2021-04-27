<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyActionIdNotificationLogsTable extends Base
{
    protected $_table = 'notification_logs';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('action_id', 1000)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('action_id', 255)->nullable()->change();
        });
    }
}
