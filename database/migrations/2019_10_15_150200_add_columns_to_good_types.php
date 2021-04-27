<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddColumnsToGoodTypes extends Base
{
    protected $_table = 'good_types';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->decimal('volume', 8, 2)->nullable()->after('title');
            $table->decimal('weight', 8, 2)->nullable()->after('volume');
            $table->integer('goods_unit_id')->nullable()->after('weight');
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
            $table->dropColumn('volume');
            $table->dropColumn('weight');
            $table->dropColumn('goods_unit_id');
        });
    }
}
