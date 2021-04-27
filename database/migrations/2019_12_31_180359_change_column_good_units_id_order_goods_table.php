<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ChangeColumnGoodUnitsIdOrderGoodsTable extends Base
{
    protected $_table = 'order_goods';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->renameColumn('good_units_id', 'goods_unit_id');
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
            $table->renameColumn('goods_unit_id', 'good_units_id');
        });
    }
}
