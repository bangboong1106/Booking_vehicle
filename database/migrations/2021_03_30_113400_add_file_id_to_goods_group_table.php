<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddFileIdToGoodsGroupTable extends Base
{
    protected $_table = 'goods_group';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'file_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('file_id', 50)->nullable()->default(0)->after('depth');
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
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('file_id');
        });
    }
}
