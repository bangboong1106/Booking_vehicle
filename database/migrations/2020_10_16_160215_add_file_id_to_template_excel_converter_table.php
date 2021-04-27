<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddFileIdToTemplateExcelConverterTable extends Base
{
    protected $_table = 'template_excel_converter';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'file_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('file_id', 255)->nullable();
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
        if (Schema::hasColumn($this->getTable(), 'file_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('file_id');
            });
        }
    }
}
