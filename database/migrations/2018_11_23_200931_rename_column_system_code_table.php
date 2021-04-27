<?php


use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class RenameColumnSystemCodeTable extends Base
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
            $table->renameColumn('code_text', 'prefix');
            $table->renameColumn('code_number', 'suffix');
            $table->integer('suffix_length')->default(6)->after('type');
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
            $table->renameColumn('prefix', 'code_text');
            $table->renameColumn('suffix', 'code_number');
            $table->dropColumn('suffix_length');
        });
    }
}
