<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class DeleteColumnOrderTable extends Base
{
    protected $_table = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn($this->getTable(), 'extend_cost')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('extend_cost');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'goods_type')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('goods_type');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'good_unit_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('good_unit_id');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'good_code')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('good_code');
            });
        }
        if (Schema::hasColumn($this->getTable(), 'insured_goods')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('insured_goods');
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
        if (!Schema::hasColumn($this->getTable(), 'extend_cost')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->decimal('extend_cost', 18, 4)->default(0);
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'goods_type')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('goods_type')->nullable();
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'good_unit_id')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('good_unit_id')->nullable();
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'good_code')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->string('good_code')->nullable();
            });
        }
        if (!Schema::hasColumn($this->getTable(), 'insured_goods')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->smallInteger('insured_goods');
            });
        }
    }
}
