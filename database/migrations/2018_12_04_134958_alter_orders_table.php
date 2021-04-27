<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AlterOrdersTable extends Base
{
    protected $_table = 'orders';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('customer_id')->default(0)->change();
            $table->string('goods_type', 50)->nullable()->change();
            $table->integer('good_unit_id')->default(0)->change();
            $table->integer('location_arrival_id')->default(0)->change();
            $table->integer('location_destination_id')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('customer_id')->default(0)->change();
            $table->string('good_type', 50)->nullable()->change();
            $table->integer('good_unit_id')->default(0)->change();
            $table->integer('location_arrival_id')->default(0)->change();
            $table->integer('location_destination_id')->default(0)->change();
        });
    }
}
