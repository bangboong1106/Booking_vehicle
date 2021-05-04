<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;


class CreateTypeShipsTable extends \App\Database\Migration\Create
{
    protected $_table = 'type_ships';
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
            $table->string('title', 50);
            $table->string('descriptions', 100)->nullable();
            $table->decimal('amount', 18, 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_ships');
    }
}
