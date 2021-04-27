<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyGoodTypesTable extends Base
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
            $table->string('code', 50)->after('title')->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('goods_type', 250)->nullable()->change();
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
            $table->dropColumn('code');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('goods_type', 50)->nullable()->change();
        });
    }
}
