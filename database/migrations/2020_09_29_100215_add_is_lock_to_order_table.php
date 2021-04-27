<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddIsLockToOrderTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'is_lock')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->char('is_lock')->nullable()->default(0);
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
        if (Schema::hasColumn($this->getTable(), 'is_lock')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('is_lock');
            });
        }
    }
}
