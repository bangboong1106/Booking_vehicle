<?php

use App\Database\Migration\Base;
use App\Database\Migration\CustomBlueprint as Blueprint;

class AddCustomerIdTo3pApiInfoTable extends Base
{
    protected $_table = '3p_api_info';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->getTable(), function (Blueprint $table) {
            $table->integer('customer_id')->nullable()->after('partner_name');
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
            $table->dropColumn('customer_id');
        });
    }
}
