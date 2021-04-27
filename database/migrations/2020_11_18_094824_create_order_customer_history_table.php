<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateOrderCustomerHistoryTable extends \App\Database\Migration\Create
{
    protected $_table = 'order_customer_history';

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
            $table->integer('status')->nullable();
            $table->text('reason')->nullable();

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
        Schema::dropIfExists('order_customer_history');
    }
}
