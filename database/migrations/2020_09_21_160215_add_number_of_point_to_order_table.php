<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddNumberOfPointToOrderTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'number_of_delivery_points')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->decimal('number_of_delivery_points', 18, 4)->nullable();
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'number_of_arrival_points')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->decimal('number_of_arrival_points', 18, 4)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn($this->getTable(), 'number_of_delivery_points')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('number_of_delivery_points');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'number_of_arrival_points')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('number_of_arrival_points');
            });
        }
    }
}
