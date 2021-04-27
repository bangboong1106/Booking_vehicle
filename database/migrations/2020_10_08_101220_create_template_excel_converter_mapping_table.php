<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTemplateExcelConverterMappingTable extends \App\Database\Migration\Create
{
    protected $_table = 'template_excel_converter_mapping';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }
        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_excel_converter_id');
            $table->string('field', 255)->nullable();
            $table->string('column_index', 255)->nullable();
            $table->string('formula', 255)->nullable();


            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
