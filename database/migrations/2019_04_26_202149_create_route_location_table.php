<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateRouteLocationTable extends \App\Database\Migration\Create
{
    protected $_table = 'route_location';

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

            $table->integer('route_id');
            $table->integer('destination_location_id')->nullable();
            $table->string('destination_location_title')->nullable();
            $table->date('destination_location_date')->nullable();
            $table->time('destination_location_time')->nullable();
            $table->integer('arrival_location_id')->nullable();
            $table->string('arrival_location_title')->nullable();
            $table->date('arrival_location_date')->nullable();
            $table->time('arrival_location_time')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('order_code')->nullable();
            $table->decimal('order_cost', 18, 4)->default(0);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
