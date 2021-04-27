<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddIndexColumnForSystemCodeTable extends Base
{
    protected $_table = 'system_code';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->index('code');
            $table->index('prefix');
            $table->index('suffix');
            $table->index('type');
            $table->index('suffix_length');
            $table->index('active');
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
            $table->dropIndex('code');
            $table->dropIndex('prefix');
            $table->dropIndex('suffix');
            $table->dropIndex('type');
            $table->dropIndex('suffix_length');
            $table->dropIndex('active');
        });
    }
}
