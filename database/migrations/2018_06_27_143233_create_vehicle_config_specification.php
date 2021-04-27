<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVehicleConfigSpecification extends \App\Database\Migration\Create
{
    protected $_table = 'vehicle_config_specification';

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
            $table->char('active', 1)->default('1');
            $table->string('name', 255);
            $table->char('type', 1)->default('1');
            $table->char('group_unit', 1)->nullable();
            $table->char('tab_view', 1)->nullable();
            $table->char('is_required', 1)->default('0');

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
        //
    }
}
