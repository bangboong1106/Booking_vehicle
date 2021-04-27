<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateFilesTable extends \App\Database\Migration\Create
{
    protected $_table = 'files';
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

        Schema::create($this->_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name', 255);
            $table->char('file_type', 10);
            $table->bigInteger('mime');
            $table->string('path');
            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
