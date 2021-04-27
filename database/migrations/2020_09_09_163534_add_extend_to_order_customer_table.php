<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddExtendToOrderCustomerTable extends Base
{
    protected $_table = 'order_customer';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'order_codes')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('order_codes', 500)->nullable();
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'customer_ids')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('customer_ids')->nullable();
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'count_order')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('count_order')->default(0);
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'vin_nos')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('vin_nos', 500)->nullable();
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'model_nos')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('model_nos', 500)->nullable();
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
            $table->dropColumn('order_codes');
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('customer_ids');
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('count_order');
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('vin_nos');
        });
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->dropColumn('model_nos');
        });
    }
}
