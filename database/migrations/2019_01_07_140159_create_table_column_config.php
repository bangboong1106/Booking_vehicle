<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTableColumnConfig extends \App\Database\Migration\Create
{
    protected $_table = 'column_config';

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

            $table->integer('user_id');
            $table->integer('table_id');
            $table->string('config', '500');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();

        });
    }
}
