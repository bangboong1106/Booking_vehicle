<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddReadyStatusColumnToDriversTable extends Base
{
    protected $_table = 'drivers';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'ready_status')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('ready_status')->default(1)->nullable()->after('active');
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
        if (Schema::hasColumn($this->getTable(), 'ready_status')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('ready_status');
            });
        }
    }
}
