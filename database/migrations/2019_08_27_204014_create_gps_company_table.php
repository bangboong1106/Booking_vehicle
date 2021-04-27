<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateGpsCompanyTable extends \App\Database\Migration\Create
{
    protected $_table = 'gps_company';

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

            $table->string('name');
            $table->string('web_service_wsdl')->nullable();
            $table->string('user')->nullable();
            $table->string('key')->nullable();
            $table->string('function_name')->nullable();
            $table->string('vehicle_function_name')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gps_company');
    }
}
