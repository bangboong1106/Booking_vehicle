<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateQuotaLocationTable extends \App\Database\Migration\Create
{
    protected $_table = 'quota_location';

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

            $table->integer('quota_id');
            $table->integer('location_id');
            $table->string('location_title')->nullable();
            $table->integer('location_order');

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
