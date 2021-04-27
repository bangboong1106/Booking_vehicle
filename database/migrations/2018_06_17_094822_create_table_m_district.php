<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateTableMDistrict extends \App\Database\Migration\Create
{
    protected $_table = 'm_district';
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
            $table->string('district_id', 5)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('province_id', 5)->nullable();
            $table->string('type', 30)->nullable();
            $table->string('location', 30)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
