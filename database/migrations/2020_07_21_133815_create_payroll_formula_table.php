<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreatePayrollFormulaTable extends \App\Database\Migration\Create
{
    protected $_table = 'payroll_formula';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->integer('payroll_id');
            $table->integer('location_group_destination_id')->nullable();
            $table->integer('location_group_arrival_id')->nullable();
            $table->integer('vehicle_group_id')->nullable();
            $table->decimal('price', 18, 4)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_formula');
    }
}
