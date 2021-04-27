<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddGoodsGroupIDToGoodsTypeTable extends Base
{
    protected $_table = 'goods_type';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'goods_group_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('goods_group_id')->nullable();
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
        if (Schema::hasColumn($this->getTable(), 'goods_group_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('goods_group_id');
            });
        }
    }
}
