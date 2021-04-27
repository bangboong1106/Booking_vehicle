<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddInAmountOutAmountToGoodsTypeTable extends Base
{
    protected $_table = 'goods_type';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'in_amount')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->decimal('in_amount')->nullable();
                $table->decimal('out_amount')->nullable();
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
        if (Schema::hasColumn($this->getTable(), 'in_amount')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('in_amount');
                $table->dropColumn('out_amount');
            });
        }
    }
}
