<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddIndexColumnSearch extends Base
{
    private $_tableCustomer = 'customer';
    private $_tableOrder = 'orders';
    private $_tableVehicle = 'vehicle';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->_tableCustomer, function (Blueprint $table) {
            $table->index(['customer_code', 'full_name']);
        });
        Schema::table($this->_tableOrder, function (Blueprint $table) {
            $table->index(['order_code', 'order_no']);
        });
        Schema::table($this->_tableVehicle, function (Blueprint $table) {
            $table->index(['reg_no']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->_tableCustomer, function (Blueprint $table) {
            $table->dropIndex(['customer_code', 'full_name']);
        });
        Schema::table($this->_tableOrder, function (Blueprint $table) {
            $table->dropIndex(['order_code', 'order_no']);
        });
        Schema::table($this->_tableVehicle, function (Blueprint $table) {
            $table->dropIndex(['reg_no']);
        });
    }
}
