<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateGoodTypesTable extends \App\Database\Migration\Create
{
    protected $_table = 'good_types';
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

            $table->string('title', '250');
            $table->text('note')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
