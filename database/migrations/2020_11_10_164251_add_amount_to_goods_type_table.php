<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddAmountToGoodsTypeTable extends Base
{
    protected $_table = 'goods_type';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'customer_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('customer_id')->nullable();
                $table->decimal('amount')->nullable();
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
        if (Schema::hasColumn($this->getTable(), 'customer_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('customer_id');
                $table->dropColumn('amount');
            });
        }
    }
}
