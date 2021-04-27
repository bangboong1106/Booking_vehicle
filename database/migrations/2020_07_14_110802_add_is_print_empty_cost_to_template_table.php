<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddIsPrintEmptyCostToTemplateTable extends Base
{
    protected $_table = 'templates';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'is_print_empty_cost')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->char('is_print_empty_cost')->nullable()->after('export_type');
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
        if (Schema::hasColumn($this->getTable(), 'is_print_empty_cost')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('is_print_empty_cost');
            });
        }
    }
}
