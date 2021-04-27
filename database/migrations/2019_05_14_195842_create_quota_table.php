<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateQuotaTable extends \App\Database\Migration\Create
{
    protected $_table = 'quota';

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

            $table->string('quota_code');
            $table->string('name');
            $table->integer('vehicle_group_id')->nullable();
            $table->text('title')->nullable();
            $table->text('location_ids')->nullable();
            $table->decimal('total_cost')->default(0);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
