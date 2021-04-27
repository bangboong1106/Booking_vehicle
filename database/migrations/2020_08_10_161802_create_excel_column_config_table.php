<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class CreateExcelColumnConfigTable extends Base
{
    protected $_table = 'excel_column_config';

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

            $table->string('model', 255);
            $table->integer('user_id')->nullable();
            $table->char('is_system')->default(1);
            $table->integer('header_index');
            $table->integer('max_row');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down()
    {
        Schema::dropIfExists('excel_column_config');
    }
}
