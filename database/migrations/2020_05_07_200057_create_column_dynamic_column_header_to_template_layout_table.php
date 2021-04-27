<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateColumnDynamicColumnHeaderToTemplateLayoutTable extends \App\Database\Migration\Create
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
            $table->char('dynamic_column_header')->nullable();
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
            $table->dropColumn('dynamic_column_header');
        });
    }
}
