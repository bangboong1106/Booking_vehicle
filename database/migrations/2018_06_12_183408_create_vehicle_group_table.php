<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateVehicleGroupTable extends \App\Database\Migration\Create
{
    protected $_table = 'm_vehicle_group';
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

        Schema::create('m_vehicle_group', function (Blueprint $table) {
            // These columns are needed for Baum's Nested Set implementation to work.
            // Column names may be changed, but they *must* all exist and be modified
            // in the model.
            // Take a look at the model scaffold comments for details.
            // We add indexes on parent_id, lft, rgt columns by default.
            $table->increments('id');
            $table->string('name', 256);
            $table->integer('parent_id')->nullable()->index();
            $table->integer('lidx')->nullable()->index();
            $table->integer('ridx')->nullable()->index();
            $table->integer('depth')->nullable();

            $table->actionBy();
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vehicle_group');
    }

}
