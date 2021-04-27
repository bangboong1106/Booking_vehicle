<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVehicleFileTable extends \App\Database\Migration\Create
{
    protected $_table = 'vehicle_file';

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
            $table->integer('vehicle_config_file_id');
            $table->integer('file_id');
            $table->char('ref_no', 100)->nullable();
            $table->text('note')->nullable();
            $table->date('expire_date')->nullable();
            $table->date('register_date')->nullable();

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
        Schema::dropIfExists('vehicle_file');
    }
}
