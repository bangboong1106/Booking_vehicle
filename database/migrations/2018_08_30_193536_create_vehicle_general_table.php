<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVehicleGeneralTable extends \App\Database\Migration\Create
{
    protected $_table = 'vehicle_general_info';

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

            $table->integer('vehicle_id');
            $table->integer('register_year')->nullable();
            $table->char('brand')->nullable();
            $table->string('weight_lifting_system', 500)->nullable();
            $table->string('category_of_barrel', 500)->nullable();
            $table->integer('max_fuel')->nullable();
            $table->date('last_register_date')->nullable();
            $table->date('expire_register_date')->nullable();
            $table->date('last_insurance_date')->nullable();
            $table->date('expire_insurance_date')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_general_info');
    }
}
