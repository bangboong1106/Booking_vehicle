<?php

use App\Database\Migration\CustomBlueprint as Blueprint;

class CreateCustomerGroupTable extends \App\Database\Migration\Create
{
    protected $_table = 'customer_group';

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->increments('id');

            $table->string('code');
            $table->text('name');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_group');
    }
}
