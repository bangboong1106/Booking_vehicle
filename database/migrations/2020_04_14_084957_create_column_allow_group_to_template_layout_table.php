<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateColumnAllowGrouptoTemplateLayouttable extends \App\Database\Migration\Create
{
    protected $_table = 'templates_layouts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->char('allow_group')->nullable();
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
            $table->dropColumn('allow_group');
        });
    }
}
