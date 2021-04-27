<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddOriginalFieldToExcelTable extends Base
{
    protected $_table = 'excel_column_mapping_config';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'original_field')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('original_field', 255)->nullable();
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
        if (Schema::hasColumn($this->getTable(), 'original_field')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('original_field');
            });
        }
    }
}
