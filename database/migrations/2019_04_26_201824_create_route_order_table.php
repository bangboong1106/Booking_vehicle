<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;

class CreateRouteOrderTable extends \App\Database\Migration\Create
{
    protected $_table = 'route_order';

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
            $table->integer('order_id');
            $table->string('order_code')->nullable();

            $table->actionBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
