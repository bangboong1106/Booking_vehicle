<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTemplatePaymentTable extends \App\Database\Migration\Create
{
    protected $_table = 'template_payment';

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
            $table->string('matching_column_index')->nullable();
            $table->integer('header_row_index')->nullable();
            $table->string('file_id', 255)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
