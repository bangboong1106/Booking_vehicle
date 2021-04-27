<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnIsInsuredGoodsToOrderTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn($this->getTable(), 'is_insured_goods'))
            return;
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->boolean('is_insured_goods')->nullable()->after('good_details');
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
            $table->dropColumn('is_insured_goods');
        });
    }
}
