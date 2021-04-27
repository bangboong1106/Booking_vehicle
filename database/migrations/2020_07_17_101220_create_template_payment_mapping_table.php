<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTemplatePaymentMappingTable extends \App\Database\Migration\Create
{
    protected $_table = 'template_payment_mapping';

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
            $table->integer('template_payment_id');
            $table->integer('receipt_payment_id')->nullable();
            $table->string('column_index', 255)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
