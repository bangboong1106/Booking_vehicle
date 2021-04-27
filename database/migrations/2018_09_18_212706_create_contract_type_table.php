<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateContractTypeTable extends \App\Database\Migration\Create
{
    protected $_table = 'contract_type';

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

            $table->string('name')->nullable();
            $table->string('description')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
