<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnTypeToGoodsTypeTable extends Base
{
    protected $_table = 'goods_type';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'type')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('type')->nullable();
            });
        }

        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->decimal('amount', 18, 4)->nullable()->change();
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
            $table->dropColumn('type');
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->decimal('amount', 8, 2)->change();
        });
    }
}
