<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVehicleTeamTable extends \App\Database\Migration\Create
{
    protected $_table = 'vehicle_team';
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

            $table->string('name', 255);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
