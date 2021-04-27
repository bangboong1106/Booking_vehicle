<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreatePayrollTable extends \App\Database\Migration\Create
{
    protected $_table = 'payroll';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->string('code');
            $table->string('name', 500);
            $table->text('description')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->char('isApplyAll', 1)->default(0);
            $table->char('isDefault', 1)->default(0);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll');
    }
}
