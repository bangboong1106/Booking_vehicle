<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateRouteCostTable extends \App\Database\Migration\Create
{
    protected $_table = 'route_cost';

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

            $table->integer('route_id');
            $table->integer('receipt_payment_id')->nullable();
            $table->string('receipt_payment_name')->nullable();
            $table->decimal('amount', 18, 4)->default(0);
            $table->string('currency')->nullable();
            $table->integer('type')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
