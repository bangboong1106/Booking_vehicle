<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class CreateExcelColumnMappingConfigTable extends Base
{
    protected $_table = 'excel_column_mapping_config';

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
            $table->integer('excel_column_config_id');
            $table->char('is_key')->default(0)->nullable();
            $table->string('column_index', 255);
            $table->string('column_name', 255);
            $table->string('field', 255);
            $table->string('data', 255)->nullable();
            $table->string('default_value', 255)->nullable();
            $table->string('data_type', 255);
            $table->string('function', 255)->nullable();
            $table->string('header_group', 255)->nullable();
            $table->string('comment', 255)->nullable();
            $table->char('collapse')->default(0);
            $table->char('is_multiple')->default(0);
            $table->string('entity', 255)->nullable();
            $table->string('code', 255)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('mapping_data', 255)->nullable();
            $table->string('mapping_field', 255)->nullable();
            $table->integer('width')->nullable();
            $table->char('is_import')->default(1)->nullable();
            $table->char('is_group')->default(0)->nullable();
            $table->string('nested_data_type', 255)->nullable();
            $table->string('nested_field', 255)->nullable();
            $table->string('nested_name', 255)->nullable();
            $table->string('nested_match', 255)->nullable();
            $table->string('background_color', 255)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down()
    {
        Schema::dropIfExists('excel_column_mapping_config');
    }
}
