<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateCustomerDefaultDataTable extends \App\Database\Migration\Create
{
    protected $_table = 'customer_default_data';

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
            $table->string('code', 255);
            $table->integer('client_id');
            $table->integer('customer_id');
            $table->integer('location_destination_id')->nullable();
            $table->integer('location_arrival_id')->nullable();
            $table->string('location_destination_ids', 255)->nullable();
            $table->string('location_arrival_ids', 255)->nullable();
            $table->integer('system_code_config_id')->nullable();
         

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
