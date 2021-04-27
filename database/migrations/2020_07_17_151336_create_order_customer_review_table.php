<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateOrderCustomerReviewTable extends \App\Database\Migration\Create
{
    protected $_table = 'order_customer_review';
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

            $table->integer('order_id');
            $table->integer('point');
            $table->string('description', 255);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
