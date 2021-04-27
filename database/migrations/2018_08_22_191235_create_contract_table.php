<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateContractTable extends \App\Database\Migration\Create
{
    protected $_table = 'contracts';

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

            $table->integer('customer_id')->nullable();
            $table->string('contract_no', 250)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->smallInteger('type')->nullable();
            $table->smallInteger('status')->nullable();
            $table->text('note')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
