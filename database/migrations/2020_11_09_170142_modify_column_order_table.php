<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class ModifyColumnOrderTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'partner_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('partner_id')->nullable()->after('vehicle_id');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'client_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('client_id')->nullable()->after('customer_id');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'status_partner')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('status_partner')->default(0)->after('status');
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'reason')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->text('reason')->nullable();
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
        if (Schema::hasColumn($this->getTable(), 'partner_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('partner_id');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'client_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('client_id');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'status_partner')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('status_partner');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'reason')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('reason');
            });
        }
    }
}
