<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVehicleTable extends \App\Database\Migration\Create
{
    protected $_table = 'vehicle';

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

        Schema::create($this->_table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->nullable();
            $table->char('reg_no', 20);
            $table->char('latitude', 10)->nullable();
            $table->char('longitude', 10)->nullable();
            $table->char('current_location', 255)->nullable();
            $table->char('status', 1)->default(1);
            $table->char('active', '1')->default(1);
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
        Schema::dropIfExists('vehicle');
    }
}
