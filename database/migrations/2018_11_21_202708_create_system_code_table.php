<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateSystemCodeTable extends \App\Database\Migration\Create
{
    protected $_table = 'system_code';

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

            $table->string('code', '250');
            $table->string('code_text', '250');
            $table->string('code_number', '250');
            $table->char('type', '1');
            $table->char('active', '1')->default(0);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
