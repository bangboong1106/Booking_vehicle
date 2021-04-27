<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateRouteTable extends \App\Database\Migration\Create
{
    protected $_table = 'routes';

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

            $table->string('route_code');
            $table->string('name');
            $table->integer('status')->nullable();

            $table->date('ETD_date')->nullable();
            $table->time('ETD_time')->nullable();

            $table->date('ETA_date')->nullable();
            $table->time('ETA_time')->nullable();

            $table->integer('quota_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('driver_id')->nullable();
            $table->decimal('total_cost', 18, 4)->default(0);
            $table->decimal('other_cost', 18, 4)->default(0);
            $table->decimal('order_cost', 18, 4)->default(0);
            $table->decimal('final_cost', 18, 4)->default(0);

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
