<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyOrderLocationTable extends Base
{
    protected $_table = 'order_locations';

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('ins_id');
            $table->dropColumn('upd_id');
            $table->dropColumn('ins_date');
            $table->dropColumn('upd_date');
            $table->dropColumn('del_flag');
        });
    }
}
