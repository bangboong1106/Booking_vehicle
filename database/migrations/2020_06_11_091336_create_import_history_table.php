<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateImportHistoryTable extends \App\Database\Migration\Create
{
    protected $_table = 'import_history';
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

            $table->string('file_id', 255);
            $table->char('type', 10);
            $table->integer('success_record');
            $table->integer('error_record');
            $table->string('memo', 255);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
