<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateOrderCustomerGoodsTable extends \App\Database\Migration\Create
{
    protected $_table = 'order_customer_goods';

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
            $table->integer('order_customer_id');
            $table->integer('goods_type_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('goods_unit_id')->nullable();
            $table->smallInteger('insured_goods')->nullable();
            $table->decimal('weight', 18, 4)->nullable();
            $table->decimal('volume', 18, 4)->nullable();
            $table->decimal('total_weight', 18, 4)->nullable();
            $table->decimal('total_volume', 18, 4)->nullable();
            $table->integer('quantity_out')->nullable();
            $table->decimal('price', 18, 4)->nullable();
            $table->text('note')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_customer_goods');
    }
}
