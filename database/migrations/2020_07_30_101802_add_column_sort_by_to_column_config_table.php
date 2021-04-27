<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddColumnSortByToColumnConfigTable extends Base
{
    protected $_table = 'column_config';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->string('sort_field')->nullable();
            $table->string('sort_type')->nullable();
            $table->integer('page_size')->nullable();

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
            $table->dropColumn('sort_field');
            $table->dropColumn('sort_type');
            $table->dropColumn('page_size');


        });
    }
}
