<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTableOrderLocations extends \App\Database\Migration\Create
{
    protected $_table = 'order_locations';
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
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('location_id');
            $table->smallInteger('type')->nullable();
            $table->date('date')->nullable();
            $table->date('date_reality')->nullable();
            $table->time('time')->nullable();
            $table->time('time_reality')->nullable();
            $table->text('note')->nullable();
        });
    }
}
