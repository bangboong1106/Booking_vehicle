<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTableCurrency extends \App\Database\Migration\Create
{
    protected $_table = 'currency';

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

            $table->string('currency_code', '15');
            $table->string('currency_name', '50');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();

        });
    }
}
