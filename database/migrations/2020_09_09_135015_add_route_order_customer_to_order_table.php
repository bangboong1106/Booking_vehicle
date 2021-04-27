<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddRouteOrderCustomerToOrderTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'route_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('route_id')->nullable()->after('vehicle_id');
                $table->index(['route_id']);
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'order_customer_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('order_customer_id')->nullable()->after('route_id');
                $table->index(['order_customer_id']);
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'source_create')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('source_create')->default(0);
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
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('route_id');
            $table->dropIndex(['route_id']);
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('order_customer_id');
            $table->dropIndex(['order_customer_id']);
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('source_create');
        });
    }
}
