<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddWeighVolumeToOrderGoods extends Base
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
            $table->decimal('weight', 18, 4)->nullable();
            $table->decimal('volume', 18, 4)->nullable();
            $table->unsignedBigInteger('quantity')->nullable()->change();
            $table->unsignedBigInteger('good_units_id')->nullable()->change();
            $table->smallInteger('insured_goods')->nullable()->change();
            $table->text('note')->nullable()->change();
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
            $table->dropColumn('weight');
            $table->dropColumn('volume');
        });
    }
}
