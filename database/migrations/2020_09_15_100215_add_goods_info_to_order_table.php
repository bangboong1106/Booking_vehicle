<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddGoodsInfoToOrderTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'quantity_order_customer')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->decimal('quantity_order_customer', 18, 4)->nullable()->before('volume');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'weight_order_customer')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->decimal('weight_order_customer', 18, 4)->nullable()->before('quantity_order_customer');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'volume_order_customer')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->decimal('volume_order_customer', 18, 4)->nullable()->before('weight_order_customer');
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
        if (Schema::hasColumn($this->getTable(), 'quantity_order_customer')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('quantity_order_customer');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'weight_order_customer')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('weight_order_customer');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'volume_order_customer')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('volume_order_customer');
            });
        }
    }
}
