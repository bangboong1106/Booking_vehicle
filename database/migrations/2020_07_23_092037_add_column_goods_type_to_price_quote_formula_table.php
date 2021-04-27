<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnGoodsTypeToPriceQuoteFormulaTable extends Base
{
    protected $_table = 'price_quote_formula';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('goods_type_id')->nullable()->after('vehicle_group_id');
            $table->dropColumn('distance');
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
            $table->dropColumn('goods_type_id');
            $table->integer('distance')->nullable();
        });
    }
}
