<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateLocationTable extends \App\Database\Migration\Create
{
    protected $_table = 'locations';
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

            $table->string('title', 255)->nullable();

            $table->text('address')->nullable();
            $table->string('address_auto_code', 100)->nullable();
            $table->string('province_id', 5)->nullable();
            $table->string('district_id', 5)->nullable();
            $table->string('ward_id', 5)->nullable();

            $table->string('location', 250)->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('type', 30)->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
