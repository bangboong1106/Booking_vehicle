<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnErrorDescriptionToQueueTable extends Base
{
    protected $_table = 'queue';

    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->text('error_description')->after('created_at');
        });
    }

    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('error_description');
        });
    }
}
