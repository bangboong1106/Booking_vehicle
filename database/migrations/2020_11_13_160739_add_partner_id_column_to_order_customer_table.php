<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddPartnerIdColumnToOrderCustomerTable extends Base
{
    protected $_table = 'order_customer';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'client_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('client_id')->nullable()->after('customer_id');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'status_goods')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('status_goods')->default(1)->after('status');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'reason')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->text('reason')->nullable()->after('status_goods');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'goods_detail')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->text('goods_detail')->nullable()->after('reason');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'note')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->text('note')->nullable()->after('goods_detail');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'customer_ids')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('customer_ids');
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
        if (Schema::hasColumn($this->getTable(), 'client_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('client_id');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'status_goods')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('status_goods');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'reason')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('reason');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'goods_detail')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('goods_detail');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'note')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('note');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'customer_ids')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('customer_ids', 255)->nullable()->after('order_codes');
            });
        }
    }
}
