<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddOrderNotesToRouteTable extends Base
{
    protected $_table = 'routes';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'order_notes')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->text('order_notes')->nullable()->after('order_codes');
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
        if (Schema::hasColumn($this->getTable(), 'order_notes')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('order_notes');
            });
        }
    }
}
