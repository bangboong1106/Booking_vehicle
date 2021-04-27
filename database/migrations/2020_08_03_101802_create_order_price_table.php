<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class CreateOrderPriceTable extends Base
{
    protected $_table = 'order_price';

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
            $table->integer('price_quote_id');
            $table->integer('amount');
            $table->char('is_approved', 1)->default(0);
            $table->integer('approved_id')->nullable();
            $table->date('approved_date')->nullable();
            $table->text('approved_note')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists($this->getTable());
    }
}
