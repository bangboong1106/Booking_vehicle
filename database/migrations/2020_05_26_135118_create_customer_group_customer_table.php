<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateCustomerGroupCustomerTable extends \App\Database\Migration\Create
{
    protected $_table = 'customer_group_customer';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->integer('customer_group_id');
            $table->integer('customer_id');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_group_customer');
    }
}
