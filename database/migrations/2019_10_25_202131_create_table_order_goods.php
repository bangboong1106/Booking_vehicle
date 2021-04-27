<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTableOrderGoods extends \App\Database\Migration\Create
{
    protected $_table = 'order_goods';
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
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('good_types_id');
            $table->unsignedBigInteger('quantity');
            $table->unsignedBigInteger('good_units_id');
            $table->smallInteger('insured_goods');
            $table->text('note');

            $table->primary(['order_id', 'good_types_id']);
        });
    }
}
