<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTemplatesLayoutsTable extends \App\Database\Migration\Create
{
    protected $_table = 'templates_layouts';

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

            $table->string('table_name', 255)->nullable();
            $table->string('column_name', 255)->nullable();
            $table->string('display_name', 255)->nullable();
            $table->string('merge_name', 255)->nullable();
            $table->integer('sort_order')->nullable();
            $table->string('field_type')->nullable();
            $table->string('data_type')->nullable();
            $table->char('type')->nullable();


            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
