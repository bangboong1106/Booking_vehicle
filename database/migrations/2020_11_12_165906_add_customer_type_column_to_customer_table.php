<?php

use \App\Database\Migration\CustomBlueprint as Blueprint;
use \App\Database\Migration\Base;

class AddCustomerTypeColumnToCustomerTable extends Base
{
    protected $_table = 'customer';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn($this->getTable(), 'customer_type')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->integer('customer_type')->default(1)->after('parent_id');
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
        if (Schema::hasColumn($this->getTable(), 'customer_type')) {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $table->dropColumn('customer_type');
            });
        }
    }
}
