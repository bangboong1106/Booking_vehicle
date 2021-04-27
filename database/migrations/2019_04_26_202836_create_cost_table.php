<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateCostTable extends \App\Database\Migration\Create
{
    protected $_table = 'cost';

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

            $table->integer('receipt_payment_id')->nullable();
            $table->string('receipt_payment_name')->nullable();
            $table->decimal('amount')->default(0);
            $table->integer('currency_id')->nullable();
            $table->string('currency_code')->nullable();
            $table->integer('type')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
