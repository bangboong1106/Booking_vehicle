<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateSystemCodeConfig extends \App\Database\Migration\Create
{
    protected $_table = 'system_code_config';

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

            $table->string('prefix', '250');
            $table->integer('suffix_length')->default(6);
            $table->char('type', '1');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
