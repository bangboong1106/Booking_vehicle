<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTemplateExcelConverterTable extends \App\Database\Migration\Create
{
    protected $_table = 'template_excel_converter';

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

            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('model')->nullable();
            $table->integer('header_row_index')->nullable();
            $table->integer('max_row')->nullable();
            $table->char('is_use_convert_sheet')->nullable();
            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
