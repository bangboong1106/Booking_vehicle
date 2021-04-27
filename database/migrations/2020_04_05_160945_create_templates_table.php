<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTemplatesTable extends \App\Database\Migration\Create
{
    protected $_table = 'templates';

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
            $table->string('file_id', 255)->nullable();
            $table->text('description')->nullable();
            $table->char('type')->nullable();
            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
