<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateColumnExportType extends \App\Database\Migration\Create
{
    protected $_table = 'templates';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('export_type')->nullable()->after('type');
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
            $table->dropColumn('export_type');
        });
    }
}
