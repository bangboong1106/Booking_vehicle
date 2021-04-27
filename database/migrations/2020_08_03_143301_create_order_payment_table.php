<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateOrderPaymentTable extends \App\Database\Migration\Create
{
    protected $_table = 'order_payment';

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
            $table->char('payment_type', 1)->default(1);
            $table->integer('payment_user_id')->nullable();
            $table->decimal('goods_amount', 18, 4)->default(0);
            $table->char('vat', 1)->default(0);
            $table->decimal('fee_ship', 18, 4)->default(0);

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
        Schema::dropIfExists('order_payment');
    }
}
