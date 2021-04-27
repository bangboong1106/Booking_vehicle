<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class DeleteCostInRouteTable extends Base
{
    protected $_table = 'routes';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('total_cost');
            $table->dropColumn('order_cost');
            $table->dropColumn('other_cost');
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
            $table->decimal('total_cost', 18, 4)->nullable();
            $table->decimal('order_cost', 18, 4)->nullable();
            $table->decimal('other_cost', 18, 4)->nullable();
        });
    }
}
