<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddQuantityToOrderCustomerTable extends Base
{
    protected $_table = 'order_customer';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'quantity')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('quantity')->nullable()->before('weight');
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
        if (Schema::hasColumn($this->getTable(), 'quantity')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }
}
