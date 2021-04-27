<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreatePayrollCustomerGroupTable extends \App\Database\Migration\Create
{
    protected $_table = 'payroll_customer_group';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->integer('payroll_id');
            $table->integer('customer_group_id')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_customer_group');
    }
}
